<?php 

namespace App\Controllers;

use App\Controllers\BaseController;
use \Core\Database\Transaction;
use App\Models\Pessoa;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\Fornecimento;
use App\Models\LogradouroPessoa;
use App\Models\Pedido;
use App\Models\DetalhesPedido;
use \App\Models\Usuario;
use \App\Models\ProdutoCategoria;
use \App\Models\Comentario;
use \App\Models\FormPgto;
use \App\Models\PedidoFormPgto;
use \App\Models\ContaPagarReceber;
use \Core\Utilitarios\Utils;
use Core\Utilitarios\Sessoes;
use \Exception;

class PessoaController extends BaseController
{
    
	public function pedidos($request)
    {
    	try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

    		if((!isset($request['get']['cliente'])) || (empty($request['get']['cliente']))){

    			throw new Exception('Parâmetro inválido');
    			
    		}

    		Transaction::startTransaction('connection');

    		$pessoa = new Pessoa();

    		$idPessoa = (int) $request['get']['cliente'];
    		$resultPessoa = $pessoa->findPessoa($idPessoa);

    		//busca todos os pedidos com status de venda
    		$this->view->pedidos = $resultPessoa->infoPedidoComplete();
    		$this->view->pessoa = $resultPessoa;
    		$this->render('pessoa/pedido/index', false);
    		Transaction::close();
    		
    	} catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }

    public function prevendas($request)
    {
    	try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            if((!isset($request['get']['cliente'])) || (empty($request['get']['cliente']))){

                throw new Exception('Parâmetro inválido');
                
            }

            //abre a conexao com o banco de dados
    		Transaction::startTransaction('connection');

            $pessoa = new Pessoa();

            $idPessoa = (int) $request['get']['cliente'];
            $resultPessoa = $pessoa->findPessoa($idPessoa);

            //busca todos os pedidos com status de venda
            $tipo = 'prevenda';
            $this->view->tipo = $tipo;
            $this->view->pedidos = $resultPessoa->infoPedidoComplete([], $tipo);
            $this->view->pessoa = $resultPessoa;
            $this->render('pessoa/pedido/index', false);

            //fax o commit e fecha a conexao com o banco
    		Transaction::close();
    		
    	} catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }
	public function orcamentos($request)
    {
    	try {

            Sessoes::sessionInit();//inicia a sessao

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad('user_admin');
            if($usuario == false){
                header('Location:/usuario/index');
                
            }

            if((!isset($request['get']['cliente'])) || (empty($request['get']['cliente']))){

                throw new Exception('Parâmetro inválido');
                
            }

            //abre a conexao com o banco de dados
            Transaction::startTransaction('connection');

            $pessoa = new Pessoa();

            $idPessoa = (int) $request['get']['cliente'];
            $resultPessoa = $pessoa->findPessoa($idPessoa);

            //busca todos os pedidos com status de venda
            $tipo = 'orcamento';
            $this->view->tipo = $tipo;
            $this->view->pedidos = $resultPessoa->infoPedidoComplete([], $tipo);
            $this->view->pessoa = $resultPessoa;
            $this->render('pessoa/pedidos', false);

            //fax o commit e fecha a conexao com o banco
            Transaction::close();
            
        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }
    }

	public function cadastro()
    {
    	try {
            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }


    		Transaction::startTransaction('connection');
            $pessoa = new Pessoa();

            $this->view->pessoa = $pessoa->findPessoa($usuario->getIdPessoa());
            $this->render('pessoa/cadastro/index', false);

    		Transaction::close();
    		
    	} catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }

    public function endereco()
    {
        try {
            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            Transaction::startTransaction('connection');

            $this->view->pessoa = $usuario;
            $this->render('pessoa/endereco/index', false);
            Transaction::close();
            
        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }
    }

	public function compras()
    {
    	try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

    		Transaction::startTransaction('connection');

            //busca todos os pedidos com status de venda
            $this->view->pedidos = $usuario->infoPedidoComplete();
            $this->view->pessoa = $usuario;

            $this->render('pessoa/pedido/index', false);

    		Transaction::close();
    		
    	} catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }

    public function pagamento()
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            Transaction::startTransaction('connection');

            //busca todos os pedidos com status de venda
            $this->view->pedidos = $usuario->infoPedidoComplete();
            $this->view->pessoa = $usuario;

            $this->render('pessoa/pagamento/index', false);

            Transaction::close();
            
        } catch (\Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }
    }


    public function cadastrar()
    {
        $this->render('pessoa/cadastro/cadastrar', false);
    }

    public function salvar($request)
    {
        try {

            if((!isset($request['post'])) || (empty($request['post']))){

                throw new Exception('Parâmetro inválido');
                
            }

            $dados = $request['post'];

            Transaction::startTransaction('connection');
            $pessoa = new Pessoa();
            $pessoa->setNomePessoa($dados['nome']);
            $pessoa->setLogin($dados['login']);
            $pessoa->setDocumento($dados['documento']);
            $pessoa->setDocumentoComplementar($dados['documento_complementar']);
            $pessoa->setNomeComplementar($dados['nome_complementar']);
            $pessoa->setSenha($dados['senha']);

            if(isset($dados['img'])){
                $pessoa->setImg($dados['img']);
            }else{
                $pessoa->setImg('avatar.png');
            }

            if(isset($dados['grupo'])){
                $pessoa->setGrupo($dados['grupo']);
            }else{
                $pessoa->setGrupo('Cliente');
            }

            if(isset($dados['tipo'])){
                $pessoa->setTipo($dados['tipo']);
            }else{
                $pessoa->setTipo('F');
            }

            if(isset($dados['sexo'])){
                $pessoa->setSexo($dados['sexo']);
            }else{
                $pessoa->setSexo('N');
            }

            $pessoa->save([]);

            $this->view->result = json_encode(['msg', 'success', '<h3>Cadastro efetuado com sucesso</h3> <p> Obs: confirme seu emil através do código enviado.</p>
                <p><a href= "/pessoa/verificar/codigo" class="btn btn-sm btn-primary" >Validar codigo</a> <a href= "/" class="btn btn-sm btn-secondary" >Voltar</a></p>
             ']);
            $this->render('pessoa/ajax', false);

            Transaction::close();
            
        } catch (Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }
    }

    public function editar()
    {   
        try {
            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            Transaction::startTransaction('connection');
            $pessoa = new Pessoa();

            $registros = $pessoa->findPessoa($usuario->getIdPessoa());

            $this->view->registros = $registros;
            $this->render('pessoa/cadastro/editar', false);

            Transaction::close();
            
        } catch (Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }

        
    }


    public function atualizar($request)
    {
        try {

            //busca o usuario logado
            $usuario = Sessoes::usuarioLoad();
            if($usuario == false){
                header('Location:/home/init');
                
            }

            if((!isset($request['post'])) || (empty($request['post']))){

                throw new Exception('Parâmetro inválido');
                
            }

            $dados = $request['post'];


            Transaction::startTransaction('connection');
            
            $usuario->setNomePessoa($dados['nome']);
            $usuario->setLogin($dados['login']);
            $usuario->setDocumento($dados['documento']);
            $usuario->setDocumentoComplementar($dados['documento_complementar']);
            $usuario->setNomeComplementar($dados['nome_complementar']);

            if(isset($dados['senha']) && (strlen($dados['senha']) > 0)){
               $usuario->setSenha($dados['senha']);
            }

            if(isset($dados['img']) && (strlen($dados['img']) > 0)){
                $usuario->setImg($dados['img']);
            }else{
                $usuario->setImg('avatar.png');
            }

            if(isset($dados['grupo'])){
                $pessoa->setGrupo($dados['grupo']);
            }else{
                $usuario->setGrupo('Cliente');
            }

            if(isset($dados['tipo'])){
                $usuario->setTipo($dados['tipo']);
            }else{
                $usuario->setTipo('F');
            }

            if(isset($dados['sexo'])){
                $usuario->setSexo($dados['sexo']);
            }else{
                $usuario->setSexo('N');
            }

            $result = $usuario->modify([]);

            if($result == true){
                $this->view->result = json_encode(['msg', 'success', '<h3>Cadastro atualizado com sucesso</h3> ']);
            }else{
                $this->view->result = json_encode(['msg', 'warning', '<h3>Nenhuma modificação foi efetuada!</h3> ']);
            }

            $this->render('pessoa/ajax', false);

            Transaction::close();
            
        } catch (Exception $e) {
            Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
        }
    }

	public function nfs()
    {
    	try {

    		Transaction::startTransaction('connection');

    		Transaction::close();
    		
    	} catch (\Exception $e) {
    		Transaction::rollback();

            $erro = ['msg','warning', $e->getMessage().' - '.$e->getLine()];
            $this->view->result = json_encode($erro);
            $this->render('pessoa/ajax', false);
    	}
    }

    

}