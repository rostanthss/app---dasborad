<?php

    //classe dashboard

    class Dashboard {

        public $data_inicio;
        public $data_fim;
        public $numero_de_vendas;
        public $total_de_vendas;
        public $clientes_ativos;
        public $clientes_inativos;
        public $total_despesas;

        public function __get($atributo){
            return $this->$atributo;
        }

        public function __set($atributo,$valor){
            $this->$atributo = $valor;
            return $this;
        }
    }
    //classe responsável pela conexão com o banco de dados
    class Conexao{
        private $host = 'localhost';
        private $dbname = 'dashboard';
        private $user = 'root';
        private $pass = '';
        private $dns = 'mysql:host=localhost;dbname=dashboard';
        public function conectar(){
            try {
                $conexao = new PDO("mysql:host=$this->host;dbname=$this->dbname","$this->user","$this->pass");
                $conexao->exec('set charset utf8');
                return $conexao;
            } catch (PDOException $e) {
                echo '<p>'. $e->getMessage() .'</p>';
            }
        }
    };

    //classe model
    class Bd{
        private $conexao;
        private $dashboard;

        public function __construct(Conexao $conexao,Dashboard $dashboard){
            $this->conexao = $conexao->conectar();
            $this->dashboard = $dashboard;
        }

        public function getNumeroVendas(){
            $query = 'select count(*) as numero_vendas from tb_vendas where data_venda between :data_inicio and :data_fim';
            
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio',$this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim',$this->dashboard->__get('data_fim'));
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;

        }

        public function getTotalVendas(){
            $query = 'select sum(total) as total_vendas from tb_vendas where data_venda between :data_inicio and :data_fim';
            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio',$this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim',$this->dashboard->__get('data_fim'));
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
        }

        public function getClientesAtivos(){
            $query = 'select count(*) as total_clientes from tb_clientes where cliente_ativo = 1';
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ)->total_clientes;
        }

        public function getClientesInativos(){
            $query = 'select count(*) as total_clientes from tb_clientes where cliente_ativo = 0';
            $stmt = $this->conexao->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ)->total_clientes;
        }

        public function getDespesas(){
            $query = "select sum(total) as despesas from tb_despesas where data_despesa between ? and ?";
            $stmt = $this->conexao->prepare($query); 
            $stmt->bindValue(1,$this->dashboard->__get('data_inicio'));
            $stmt->bindValue(2,$this->dashboard->__get('data_fim'));
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ)->despesas;
        }
    }

    $dashboard = new Dashboard();
    $conexao = new Conexao();

    $data = explode('-',$_GET['datas']);
    $numero_dias_mes = cal_days_in_month(CAL_GREGORIAN,$data[1],$data[0]);
    $dashboard->__set('data_inicio','2018-'.$data[1].'-01');
    $dashboard->__set('data_fim','2018-'.$data[1].'-'.$numero_dias_mes);
    $bd = new Bd($conexao,$dashboard);
    $dashboard->__set('numero_de_vendas',$bd->getNumeroVendas());
    $dashboard->__set('total_de_vendas',$bd->getTotalVendas());
    $dashboard->__set('clientes_ativos',$bd->getClientesAtivos());
    $dashboard->__set('clientes_inativos',$bd->getClientesInativos());
    $dashboard->__set('total_despesas',$bd->getDespesas());

    echo json_encode($dashboard);
    

?>