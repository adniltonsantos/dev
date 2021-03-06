<?php
/**
 * ORM simples para gerir querys SQL
 * 
 * @author Guilherme Brito
 * @version 1.0.16
 */
namespace GORM;
use Exception;
include(dirname(dirname(__FILE__)).'/gorm.php');
class Model 
{
use Database, Init, Persistent, Builder, Finder;
    /**
     * Variável para armazenar as configurações
     * sql = recebe o query sql que será executada
     *  
     * @var Mixed
     */
    private $configuration;
    /**
     * Metodo que implementa o metodo Singleton
     *
     * @return Instancia 
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        //Verifica se as configurações já foram carregadas
        if(empty($instance->configuration)){
            $instance->loadConf();
        }
        // Por padrão o chave primaria é setada como id
        $instance->setPrimaryKey('id');
        return $instance;
    }
    /**
     * Função para carregas as informações do arquivo de configuração 
     * gorm.conf
     *
     * @return Mixed
     */
    public function loadConf(){
        $file =  dirname(dirname(__FILE__))."/gorm.conf";
        //Checa a existencia do arquivo de configuração
        if (file_exists($file)){
            // Checa se é possivel ler o arquivo de configuração
            if (is_readable($file)){
                $arch = file($file);
                foreach ($arch as $key => $value) {
                    //Verifica se a linha do arquivo começa com # (se é um comentário)
                    if (substr($value, 0, 1)!= "#"){
                        $arr = explode('=', $value);
                        $conf[trim($arr[0])] = trim($arr[1]);
                    }
                }
                $this->configuration = $conf;
            }else{
                throw new Exception("Não foi possivel ler o arquivo de configuração", 002);
            }
        }else{
            throw new Exception("Arquivo não encontrado", 001);
        }
    }
    /**
     * Retorna uma instancia da classe que esta solicitando uma determinada função
     *
     * @return void
     */
    public static function getCalledClass(){
        $cls = get_called_class();
        $cls = $cls::getInstance();
		return $cls;
    }
    /**
     * Metodo para carregar a tabela que será trabalhada
     *
     * @return void
     */
    public function loadTable(){
        $this->configuration['table'] = get_called_class();
        $this->configuration['table'] = str_replace("\\", "/", strtolower(get_called_class()));
		$this->configuration['table'] = explode('/', $this->configuration['table']);
		$this->configuration['table'] = $this->configuration['table'][count($this->configuration['table']) -1];     
    }
    /**
     * Seta qual é a Primary Key de uma determinada tabela;
     *
     * @param String $pk
     * @return void
     */
    public function setPrimaryKey($pk){
        $this->configuration['primaryKey'] = $pk;
    }
    /**
     * Seta qual é o campo que não pode ser repetido no banco de dados
     *
     * @param Strint $unique
     * @return void
     */
    public function setUniqueFild($unique){
        $this->configuration['uniqueFild'] = $unique;
    }
    /**
     * Carrega as informações de um objeto atraves de um array
     *
     * @param array $array
     * @return void
     */
    public function load($array = []){
        foreach ($array as $key => $value) {
            try{
                $this->$key = $value;
            }catch(Exception $e){
                
            }
        }
        $this->loadConf();
    }
    public function toArray(){
        foreach ($this as $key => $value) {
            if($key != 'configuration')
                $arr[$key] = $value;
        }
        return $arr;
    }
}