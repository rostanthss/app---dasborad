$(document).ready(() => {
	$('#documentacao').click(()=>{
        $('#pagina').load('documentacao.html')
    })

    $('#suporte').click(()=>{
        $('#pagina').load('suporte.html')
    })

    $('#datas').change((e)=>{

        $.ajax({
            type:'GET',
            url:'app.php',
            data: 'datas='+$(e.target).val(),
            dataType: 'Json',
            success: dados =>{
                $('#numeroVendas').html(dados.numero_de_vendas)
                $('#totalVendas').html(dados.total_de_vendas)
                $('#clientesAtivos').html(dados.clientes_ativos)
                $('#clientesInativos').html(dados.clientes_inativos)
                $('#despesas').html(dados.total_despesas)
                
            },
            erro: erro =>{console.log(erro)}
        })
        
        
    })
    
    
})