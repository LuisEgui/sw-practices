<?php

require_once __DIR__.'/Formulario.php';
require_once __DIR__.'/DamageService.php';
require_once __DIR__.'/DamageList.php';

class FormularioActualizarIncidente extends Formulario {

    private $damageService;

    private $damagesList;

    private $orderDamagesBy;

    public function __construct($orderByFunction) {
        parent::__construct('formUpdateDamage', ['urlRedireccion' => 'actualizarDatosIncidente.php']);
        $this->damageService = DamageService::getInstance();
        $this->damagesList = new DamageList();
        if(isset($orderByFunction)){
            $this->orderDamagesBy = $orderByFunction;
        }
    }
    
    protected function generaCamposFormulario(&$datos) {
        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores); // Se muestra como una lista
        
        // Se leen todos los vehiculos de la base de datos y se almacenan en un array de la instancia de la clase VehicleList
        $this->damagesList->setArray($this->damageService->readAllDamages());
        if(isset($this->orderVehiclesBy)){
            $this->damagesList->orderBy($this->orderVehiclesBy);
        }

        // Se genera el HTML asociado al formulario y los mensajes de error.
        $html = <<<EOS
        $htmlErroresGlobales
            <div>
            <table>
                <tr>
                    <th></th>
                    <th>ID Incidencia</th>
                    <th>ID Usuario</th>
                    <th>VIN Vehiculo</th>
                    <th>Titulo</th>
                    <th>Descripcion</th>
                    <th>Tipo</th>
                    <th>Reparado</th>
                    <th>Fecha de modificacion</th>
                </tr>
        EOS;

        foreach($this->damagesList->getArray() as $damage) {
            $state = "No";
            if($damage->getIsRepaired()){
                $state = "Si";
            }
            $html .= <<<EOS
                <tr>
                    <td><input type="radio" name="updatedDamageId" value="{$damage->getId()}" required></td>
                    <td>{$damage->getId()}</td>
                    <td>{$damage->getUser()}</td>
                    <td>{$damage->getVehicle()}</td>
                    <td>{$damage->getTitle()}</td>
                    <td>{$damage->getDescription()}</td>
                    <td>{$damage->getType()}</td>
                    <td>{$state}</td>
                    <td>{$damage->getTimeStamp()}</td>
                </tr>
            EOS;
        }
        $html .= <<<EOS
            </table>
            </div>
            <div>
                <button type="submit" name="update"> Actualizar </button>
            </div>
        EOS;

        return $html;
        
    }

    protected function procesaFormulario(&$datos) {    
        
        $this->errores = [];

        if(!isset($datos['updatedDamageId']))
            $this->errores[] = 'Debe seleccionar un incidente.';

        if (!self::validDamage($datos['updatedDamageId']))
            $this->errores[] = "El incidente a eliminar no coincide con ninguno de los existentes.";

        if (count($this->errores) === 0) { 
            $this->changeUlrRedireccion("{$this->urlRedireccion}?idDamageToUpdate={$datos['updatedDamageId']}");
            header("Location: {$this->urlRedireccion}");
        }
    }

    protected function validDamage($damage) {
        $damagesInDatabase = $this->damageService->readAllDamagesID();
        return in_array($damage, $damagesInDatabase);
    }    
}
