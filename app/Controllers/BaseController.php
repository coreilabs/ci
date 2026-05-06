<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    /**
     * Helpers globais carregados automaticamente
     */
    protected $helpers = ['url', 'form', 'permission'];

    /**
     * Sessão global
     */
    protected $session;

    /**
     * Request global
     */
    protected $request;

    /**
     * Response global
     */
    protected $response;

    /**
     * Logger
     */
    protected $logger;

    /**
     * Inicialização padrão do CI4
     */
    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        // 🔐 Serviços globais
        $this->session  = service('session');
        $this->request  = $request;
        $this->response = $response;
        $this->logger   = $logger;
    }
}