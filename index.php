<?php
    require("./conexaoBD.php"); 
    require('./apiCidades.php');  


    $conexaoBD = new ConexaoBD();
    $conn = $conexaoBD->getConexao();
    //$data = obterDadosApi();

    //echo var_dump($data);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $dados = json_decode(file_get_contents("php://input"), true);
        
        if (isset($dados["action"])) {
            $act = $dados["action"];

            switch ($act) {
                case 'enviarCadastro':
                    $result = enviarCadastro($dados);
                    break;
                case 'buscarTabela':
                    $result = buscarTabela();
                    break;
                case 'buscarCadastro':
                    $result = buscarCadastro($dados["id_cad"]);
                    break;
                case 'apagarCadastro':
                    $result = apagarCadastro($dados["id_cad"]);
                    break;
                default:
                    //NADA
                    break;
            }

            echo json_encode($result); // Retornar o resultado ao javascript em json;
            exit; // Termina a execução após enviar a resposta JSON
        }

    }

    function enviarCadastro($dados){
        global $conn;
        try {
            if($dados['id_cad'] != ""){

                $sql = "UPDATE cadastro SET
                    nome_cad    = '{$dados['nome']}',
                    cpf_cad     = '{$dados['cpf']}',
                    cell_cad    = '{$dados['celular']}',
                    cid_cad     = '{$dados['cidade']}'
                WHERE id_cad = {$dados['id_cad']}";

                $stmt = $conn->prepare($sql);
                if(!$stmt){
                    throw new Exception("Erro ao atualizar dados");
                }
                $stmt->execute();

                $retorno = [
                    'status' => true,
                    'msg' => "Cadastro atualizado com sucesso!!!"
                ];
            }else{
                $sql = "INSERT INTO `cadastro` (nome_cad, cpf_cad, cell_cad, cid_cad) 
                VALUES
                ('{$dados['nome']}', '{$dados['cpf']}', '{$dados['celular']}', '{$dados['cidade']}' )";
                
                $stmt = $conn->prepare($sql);
                if(!$stmt){
                    throw new Exception("Erro ao preparar query");
                }
                $stmt->execute();

                $retorno = [
                    'status' => true,
                    'msg' => "Cadastro enviado com sucesso!!!"
                ];
            }
        } catch (Exception $e) {
            $retorno = [
                'status' => false,
                'msg' => $e->getMessage()
            ];
        }


        return $retorno;
    }

    function buscarTabela () {
        global $conn;
        try {
            
            $sql = "SELECT * FROM `cadastro` WHERE 1";

            $stmt = $conn->prepare($sql);
            if(!$stmt){
                throw new Exception("Erro ao preparar query de busca");
            }

            $stmt->execute();

            $resposta = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $html = "";
            foreach ($resposta as $res) {
                $html .= "
                    <tr class=''>
                        <td class='colSize1 text-center'>{$res['id_cad']}</td>
                        <td class='col text-left'>{$res['nome_cad']}</td>
                        <td class='col text-center'>{$res['cpf_cad']}</td>
                        <td class='col text-center'>{$res['cell_cad']}</td>
                        <td class='col text-center'>{$res['cid_cad']}</td>
                        <td class='col text-center'>
                            <span><i class='fa-solid fa-pen-to-square text-primary icon' onclick='buscarCadastro({$res['id_cad']})'></i></span>
                            <span><i class='fa-solid fa-trash-can text-danger icon' onclick='modalDeletar({$res['id_cad']})'></i></span>
                        </td>
                    </tr>
                ";
            }
            
            $retorno = [
                'status' => true,
                'html' => $html,
            ];
        } catch (Exception $e) {
            $retorno = [
                'status' => false,
                'msg' => $e->getMessage()
            ];
        }

        return $retorno;
    }
    
    function buscarCadastro ($id_cad){
        global $conn;
        try {
            $sql = "SELECT nome_cad, cpf_cad, cell_cad, cid_cad 
                    FROM `cadastro` 
                    WHERE id_cad = {$id_cad}"; 

            $stmt = $conn->prepare($sql);
            if(!$stmt){
                throw new Exception("Erro ao buscar cadastros!!!");
            }

            $stmt->execute();

            $resposta = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $res = $resposta[0];

            $retorno = [
                'status'    => true,
                'nome_cad'  => $res['nome_cad'],
                'cpf_cad'   => $res['cpf_cad'],
                'cell_cad'  => $res['cell_cad'],
                'cid_cad'   => $res['cid_cad']
            ];
            
        } catch (Exception $e) {
            $retorno = [
                'status' => false,
                'msg' => $e->getMessage()
            ];
        }

        return $retorno;
    }

    function apagarCadastro ($id_cad){
        global $conn;
        try {
            $sql = "DELETE FROM cadastro WHERE id_cad = $id_cad";

            $stmt = $conn->prepare($sql);
            if(!$stmt){
                throw new Exception ("Erro ao deletar o registro");
            }

            $stmt->execute();
            $retorno = [
                'status' => true,
                'msg' => "Registro deletado com sucesso!!"
            ];
        } catch (Exception $e) {
            $retorno = [
                'status' => false,
                'msg' => $e->getMessage()
            ];
        }

        return $retorno;
    }

    include("./index.html")
    
    // define('dadosApi', obterDadosApi());
    // $html =  file_get_contents('./index.html');
    // $html = str_replace('{dadosApi}', dadosApi, $html);
    // echo $html
    

?>