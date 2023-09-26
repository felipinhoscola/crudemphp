<?php
// Erro em meu ssl, por isso não dei continuidade a busca da API
function obterDadosApi(){
    try {
        $api_municipios = 'https://servicodados.ibge.gov.br/api/v1/localidades/estados/35/municipios?orderBy=nome';
        
        $ch = curl_init($api_municipios);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        
        // Verifica se houve algum erro na requisição
        if ($response === false) {
            throw new Exception('Erro ao fazer a requisição à API');
        }
        $data = json_decode($response, true);


        $html = "<option selected value=''> -- Selecione --</option>";
        foreach ($data as $d) {
            $nomeCidade = $d["nome"];
            $idCidade = $d["id"];

            // Acesse a sigla da região (SP no exemplo)
            $siglaRegiao = $d["microrregiao"]["mesorregiao"]["UF"]["sigla"];

            $html .="<option value='{$idCidade}'>Cidade {$nomeCidade}, {$siglaRegiao}</option>";
        }

        return $html;

    } catch (Exception $e) {
        die($e->getMessage());
    }
    curl_close($ch);
}
?>