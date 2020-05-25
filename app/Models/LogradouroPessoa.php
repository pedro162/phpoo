<?php

namespace App\Models;

use App\Models\BaseModel;
use \Exception;
use \InvalidArgumentException;

class LogradouroPessoa extends BaseModel
{
	private $complemento;
	private $endereco;
	private $idLogradouro;

    const TABLENAME = 'LogradouroPessoa';

    private $data = [];


    protected function clear(array $dados)//Exite ao instanciar uma nova chamada de url $request['post'], $request['get']
    {
        //falta implementar corretamente
        if(!isset($dados)){
            throw new Exception('Parametro inválido<br/>');
        }
        if(count($dados) == 0){
            throw new Exception('Parametro inválido<br/>');
        }

        for ($i=0; !($i == count($dados)) ; $i++) { 

            $subArray = explode('=', $dados[$i]);
           
            switch ($subArray[0]) {
                case '':
                   break;
            }

        }
    }

    protected function parseCommit():array
    {
      

        return $this->data;
    }


    public function save(array $dados)
    {
        $this->clear($dados);

        $result = $this->parseCommit();

        $this->insert($result);
    }

    public function modify(array $dados)
    {
        
    }


    public function listarConsultaPersonalizada(String $where = null, Int $limitInt = NULL, Int $limtEnd = NULL, $clasRetorno = false)
    {

        $sql = "SELECT L.endereco, L.complemento, L.idLogradouro, P.nomePessoa
				FROM 
				LogradouroPessoa LP inner join Logradouro L on LP.LogradouroIdLogradouro = L.idLogradouro
				INNER JOIN Pessoa P on LP.PessoaIdPessoa = P.idPessoa ";

        if($where != null)
        {
            $sql .= ' WHERE '.$where;
        }

        if(($limitInt != NULL) && ($limtEnd != NULL)){

            if(($limitInt >= 0) && ($limtEnd >= 0)){
                $sql .= ' LIMIT '.$limitInt.','. $limtEnd; 
            }
        } 
        $result = $this->persolizaConsulta($sql, $clasRetorno);

        return $result;
    }

    public function getComplemento()
    {
    	if(isset($this->complemento) && (!empty($this->complemento))){
    		return $this->complemento;
    	}
    	throw new \Exception("Proriedade indefinida");
    	
    }

    public function getEndereco()
    {
    	if(isset($this->endereco) && (!empty($this->endereco))){
    		return $this->endereco;
    	}

    	throw new \Exception("Proriedade indefinida");
    }

    public function getIdLogradouro()
    {
    	if(isset($this->idLogradouro) && (!empty($this->idLogradouro))){
    		return $this->idLogradouro;
    	}

    	throw new \Exception("Proriedade indefinida");
    }

}
