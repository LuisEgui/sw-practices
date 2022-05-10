<?php

require_once RAIZ_APP.'/Formulario.php';
require_once RAIZ_APP.'/ReserveService.php';
require_once RAIZ_APP.'/UserService.php';

class FormularioActualizarReturnTimeReserva extends Formulario {

    private $reserveService;
    
    public function __construct() {
        parent::__construct('formUpdateReserveReturnTime', ['urlRedireccion' => 'index.php']);
        $this->reserveService = new ReserveService($GLOBALS['db_reserve_repository'], $GLOBALS['db_vehicle_repository'], $GLOBALS['db_user_repository']);
        $this->userService = new UserService($GLOBALS['db_user_repository'], $GLOBALS['db_image_repository']);
    }
    
    protected function generaCamposFormulario(&$datos) {

        // Se generan los mensajes de error si existen.
        $htmlErroresGlobales = self::generaListaErroresGlobales($this->errores);

        // Se genera el HTML asociado a los campos del formulario y los mensajes de error.
        $html = <<<EOF
        $htmlErroresGlobales
        <fieldset>
            <legend>Actualizar hora de devolucion</legend>
            <div>
                <label for="returnTime">Hora devolucion:</label>
                <input type="datetime-local" id="returnTime"
                name="returnTime" >
            </div>
            <div>
                <label><input type="submit" name="update" value="Actualizar"></label>
            </div>
        </fieldset>
        EOF;
        return $html;
    }


    protected function procesaFormulario(&$datos) {
        $reservePT = $datos['returnTime']; 
        if ($this->reserveService->updateReserveReturnTime($_GET["id"], $reservePT))
            header("Location: {$this->urlRedireccion}");
        else
            $this->errores[] = "Can't upload the return time of this reserve! Car is not available that date";
    }
}