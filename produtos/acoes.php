<?php

    session_start();

    require("../database/conexao.php");

    function validarCampos(){

        // echo '<pre>';
        // var_dump($_POST);
        // echo '<pre>';
        // exit;

        // foreach ($_POST as $key => $value) {
            
        //     echo "INDICE -> " .$key . ' VALR -> ' .$value .'<br>';

        //     if ($_POST["$key"] == "" || !isset($_POST["$key"])) {
                


        //     }

        // }

        //ARRAY DAS MENSAGENS DE ERRO
        $erros = [];

        //VALIDAÇÃO DE DESCRIÇÃO
        if ($_POST["descricao"] == "" || !isset($_POST["descricao"])) {

            $erros[] = "O CAMPO DESCRIÇÃO E OBRIGATORIO";

        }

        //VALIDAÇÃO DE PESO
        if ($_POST["peso"] == "" || !isset($_POST["peso"])) {

            $erros[] = "O CAMPO PESO E OBRIGATORIO";

        }elseif(!is_numeric(str_replace(",", ".", $_POST["peso"]))){

            $erros[] = "O CAMPO PESO DEVE SER UM NUMERO";

        }

        //VALIDAÇÃO DE QUANTIDADE
        if ($_POST["quantidade"] == "" || !isset($_POST["quantidade"])) {

            $erros[] = "O CAMPO QUANTIDADE E OBRIGATORIO";

        }elseif(!is_numeric(str_replace(",", ".", $_POST["quantidade"]))){

            $erros[] = "O CAMPO QUANTIDADE DEVE SER UM NUMERO";

        }

        //VALIDAÇÃO DE COR
        if ($_POST["cor"] == "" || !isset($_POST["cor"])) {

            $erros[] = "O CAMPO COR E OBRIGATORIO";

        }

        //VALIDAÇÃO TAMANHO
        if ($_POST["tamanho"] == "" || !isset($_POST["tamanho"])) {

            $erros[] = "O CAMPO TAMANHO E OBRIGATORIO";

        }

        // VALIDAÇÃO VALOR
        if ($_POST["valor"] == "" || !isset($_POST["valor"])) {

            $erros[] = "O CAMPO QUANTIDADE E OBRIGATORIO";

        }elseif(!is_numeric(str_replace(",", ".", $_POST["valor"]))){

            $erros[] = "O CAMPO VALOR DEVE SER UM NUMERO";

        }

        //VALIDAÇÃO DE DESCONTO
        if ($_POST["desconto"] == "" || !isset($_POST["desconto"])) {

            $erros[] = "O CAMPO DESCONTO E OBRIGATORIO";

        }elseif(!is_numeric(str_replace(",", ".", $_POST["desconto"]))){

            $erros[] = "O CAMPO DESCONTO DEVE SER UM NUMERO";

        }

        //VALIDAÇÃO DE CATEGORIA
        if ($_POST["categoria"] == "" || !isset($_POST["categoria"])) {

            $erros[] = "O CAMPO CATEGORIA E OBRIGATORIO";

        }

        /* VALIDAÇÃO DA IMAGEM */
        if ($_FILES["foto"]["error"] == UPLOAD_ERR_NO_FILE) {
            
            $erros[] = "O ARQUIVO PRECISA SER UMA IMAGEM";

        }else {
            
            $imagemInfos = getimagesize($_FILES["fotos"]["tmp"]);

            if ($_FILES["foto"]["size"] > 1024 * 1024 * 2) {
                
                $erros[] = "O ARQUIVO NÃO PODE SER MAIOR QUE 2MB";

            }

            $width = $imagemInfos[0];
            $height = $imagemInfos[1];

            if ($width != $height) {
                
                $erros[] = "A IMAGEM PRECISA SER QUADRADA";

            }

        }

        return $erros;

    }

    switch ($_POST["acao"]) {

        case 'inserir':

            $erros = validarCampos();

            if (count($erros) > 0) {
                
                $_SESSION["erros"] = $erros;

                header("location: novo/index.php");

                exit;

            }
            
            /* TRATAMENTO DA IMAGEM PARA UPLOAD: */

            //RECUPERA O NOME DO ARQUIVO
            $nomeArquivo = $_FILES["foto"]["name"];
            
            //RECUPERAR A EXTENSÃO DO ARQUIVO
            $extensao = pathinfo($nomeArquivo, PATHINFO_EXTENSION);

            //DEFINIR UM NOVO NOME PARA O ARQUIVO DE IMAGEM
            $novoNome = md5(microtime()) . "." . $extensao;

            //UPLOAD DO ARQUIVO:
            move_uploaded_file($_FILES["foto"]["tmp_name"], "fotos/$novoNome");

            /* INSERÇÃO DE DADOS NA BASE DE DADOS DO MYSQL: */

            //RECEBEIMENTO DOS DADOS:
            $descricao = $_POST["descricao"];
            $peso = $_POST["peso"];
            $quantidade = $_POST["quantidade"];
            $cor = $_POST["cor"];
            $tamanho = $_POST["tamanho"];
            $valor = $_POST["valor"];
            $desconto = $_POST["desconto"];
            $categoriaId = $_POST["categoria"];

            //CRIAÇÃO DA INSTRUÇÃO SQL DE INSERÇÃO:
            $sql = "INSERT INTO tbl_produto 
            (descricao, peso, quantidade, cor, tamanho, valor, desconto, imagem, categoria_id) 
            VALUES ('$descricao', $peso, $quantidade, '$cor', '$tamanho', $valor, $desconto, 
            '$novoNome', $categoriaId)";

            //EXCUÇÃO DO SQL DE INSERÇÃO:
            $resultado = mysqli_query($conexao, $sql);

            //REDIRECIONAR PARA INDEX:
            header('location: index.php');

            break;

        case 'deletar':

            $produtoID = $_POST['produtoId'];

            $sql = "SELECT imagem FROM tbl_produto WHERE id = $produtoID";

            $resultado = mysqli_query($conexao, $sql);

            $produto = mysqli_fetch_array($resultado);

            // echo $produto[0];exit;

            $sql = "DELETE FROM tbl_produto WHERE id = $produtoID";

            $resultado = mysqli_query($conexao, $sql);

            unlink("./fotos/" . $produto[0]);

            header('location: index.php');

            break;

        case 'editar':

                $idProduto = $_POST["idProduto"];

                if ($_FILES["foto"]["error"] != UPLOAD_ERR_NO_FILE) {
                    
                    $sqlImagem = "SELECT imagem FROM tbl_produto WHERE id = $idProduto";

                    $resultado = mysqli_query($conexao, $sqlImagem);
                    $produto = mysqli_fetch_array($resultado);


                }
            
                $id = $_POST["id"];
                $descricao = $_POST["descricao"];
                $peso = str_replace(".", "", $_POST["peso"]);
                $peso = str_replace(",", ".", $peso);

                $valor = str_replace(".", "", $_POST["valor"]);
                $valor = str_replace(",", ".", $valor);

                $quantidade = $_POST["quantidade"];
                $cor = ["cor"];
                $tamanho = ["tamanho"];
                $desconto = ["desconto"];
                $categoriaId = ["categoria"];
                
    
                $sql = "UPDATE tbl_produto SET descricao = '$descricao' WHERE id = $id";
                // echo $sql; exit;
    
                $resultado = mysqli_query($conexao, $sql);
    
                header('location: index.php');
    
                break;
                
            default:
                # code...
                break;    
        
        
    }


?>