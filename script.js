const urlPhp = "index.php"; // php da pagina
var myModal;

document.addEventListener("DOMContentLoaded", function() {
    var onlyNumInputs = document.querySelectorAll(".onlyNum");
    onlyNumInputs.forEach(function(input) {
        input.addEventListener("keydown", function(e) {
            if (!((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105) || (e.keyCode == 8) || (e.keyCode == 9))) {
                e.preventDefault();
            }
        });
    });

    var idCadCpf = document.getElementById("idCadCpf");
    idCadCpf.addEventListener("keyup", function(e) {
        var input = e.target;
        input.value = maskCpf(input.value);
    });

    var idCadCell = document.getElementById("idCadCell");
    idCadCell.addEventListener("keyup", function(e) {
        var input = e.target;
        input.value = maskTel(input.value);
    });

    myModal = new bootstrap.Modal(document.getElementById('modalCadastro'), {
        keyboard: false
    })  
    modalDel = new bootstrap.Modal(document.getElementById('modalDeletar'), {
        keyboard: false
    })    

    buscarTabela();
});

//Mascaras
function maskCpf(value) {
    if (!value ) return ""
    value =  value.replace(/\D/g, '');

    value =  value.replace(/(\d{3})(\d)/, '$1.$2');
    value =  value.replace(/(\d{3})(\d)/, '$1.$2');
    value =  value.replace(/(\d)(\d{2})$/, '$1-$2');
    return value;
}
function maskTel(value) {
    if (!value ) return ""
    value =  value.replace(/\D/g, '');

    value =  value.replace(/(\d{2})(\d)/, '($1) $2');
    value =  value.replace(/(\d)(\d{4})$/, '$1-$2');
    return value;
}

//Funcoes Defaults
function loader(tipo) {
    let loader = document.getElementById("loader")

    if(tipo == 'abrir'){
        loader.classList.remove('d-none')
    }else if( tipo == 'fechar'){
        loader.classList.add('d-none');
    }
}
function AlertSucesso(text) {
    let alert_success = document.getElementById('alert_success');
    let mensagem = document.getElementById('alert_succ_mensagem');
    mensagem.textContent = text
    alert_success.classList.remove("d-none")

    setTimeout(() => {
        alert_success.classList.add("d-none") 
    }, 5000);
    
}
function AlertError(text) {
    let alert_success = document.getElementById('alert_error');
    let mensagem = document.getElementById('alert_err_mensagem');
    mensagem.textContent = text
    alert_success.classList.remove("d-none")

    setTimeout(() => {
        alert_success.classList.add("d-none") 
    }, 5000);
    
}
function limparInputs() {
    document.getElementById("idCadNome").value = "";
    document.getElementById("idCadCpf").value = "";
    document.getElementById("idCadCell").value = "";
    document.getElementById("idCadCid").value = "";
    document.getElementById("hd_id").value = "";
}
function modalDeletar(id_cad) {
    document.getElementById("id_deletar").value = id_cad;
    modalDel.show();

}

//funçoes envios e recebimentos de dados
function enviarCadastro() {
    loader('abrir');

    var dados =  {
        action: "enviarCadastro",
        nome: document.getElementById("idCadNome").value,
        cpf: document.getElementById("idCadCpf").value,
        celular: document.getElementById("idCadCell").value,
        cidade: document.getElementById("idCadCid").value,
        id_cad: document.getElementById("hd_id").value
    }


    fetch(urlPhp, {
        method:'POST',
        headers : {
            "Content-Type": "aplication/json" // Configura o tipo do conteudo
        },
        body: JSON.stringify(dados) // Conerte em JSON
    })
    .then(response=>response.text())
    .then(data => {
        data = JSON.parse(data)
        if(data.status){
            AlertSucesso(data.msg)
        }else{
            AlertError(data.msg)
        }
        myModal.hide() 
        loader('fechar');
        buscarTabela();
    })
    .catch(error => {
        console.error('Erro:', error);
    });
}
function buscarTabela(){
    loader('abrir');
    var dados = {
        action: "buscarTabela"
    }

    fetch(urlPhp, {
        method: 'POST',
        headers : {
            "Content-Type": "aplication/json"
        },
        body:JSON.stringify(dados),
    })
    .then(response=>response.text())
    .then(data => {
        data = JSON.parse(data)

        if(data.status){
            document.getElementById('corpo-tabela').innerHTML = data.html;
        }else{
            AlertError(data.msg)
        }
        loader('fechar');
    })
    .catch(error => {
        console.error('Erro:', error);
    });

}
function buscarCadastro(id_cad) {
    loader('abrir');
    var dados = {
        action : "buscarCadastro",
        id_cad : id_cad,
    }

    fetch(urlPhp, {
        method:'POST',
        headers : {
            "Content-Type" : "aplication/json"
        },
        body: JSON.stringify(dados)
    })
    .then(response=>response.text())
    .then(data => {
        data = JSON.parse(data)
        if(data.status){
            limparInputs();
            document.getElementById("idCadNome").value = data.nome_cad;
            document.getElementById("idCadCpf").value = data.cpf_cad;
            document.getElementById("idCadCell").value = data.cell_cad;
            document.getElementById("idCadCid").value = data.cid_cad;
            document.getElementById("hd_id").value = id_cad;
        }else{
            AlertError(data.msg)
        }
        myModal.show() 
        loader('fechar');
    })
    .catch(error => {
        console.error('Erro:', error);
    });
}
function apagarCadastro() {
    loader('abrir');

    var dados = {
        action : 'apagarCadastro',
        id_cad : document.getElementById("id_deletar").value
    };

    fetch(urlPhp, {
        method:'POST',
        headers:{
            'Content-type': 'aplication/json'
        },
        body: JSON.stringify(dados)
    })
    .then(response=>response.text())
    .then(data => {
        data = JSON.parse(data)

        if(data.status){
            AlertSucesso(data.msg)
        }else{
            AlertError(data.msg)
        }
        buscarTabela()
        modalDel.hide();
        loader('fechar');
    })
    .catch(error => {
        console.error('Erro:', error);
    });
}

    

