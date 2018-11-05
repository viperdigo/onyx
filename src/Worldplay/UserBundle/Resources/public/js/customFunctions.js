$(function () {

    $('button').on('click', function () {

        $(this).prop('type', 'button');
        var $form = $('form');
        var values = {};
        $.each($form.serializeArray(), function (i, field) {
            values[field.name] = field.value;
        });

        $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: values,
            success: function (data) {
                if (data) {
                    var obj = jQuery.parseJSON(data); //encode json

                    var html  = '<tr>';
                        html += '   <td>'+obj.nome+'</td>';
                        html += '   <td>'+obj.descricao+'</td>';
                        html += '   <td class="table-action">';
                        html += '       <a href=""><i class="fa fa-pencil"></i></a>';
                        html += '       <a class="delete-row" href=""><i class="fa fa-trash-o"></i></a>';
                        html += '   </td>';
                        html += '</tr>';

                    //Mensagem de alerta
                    $('.alert').css('display','block');
                    $('.alert').addClass('alert-success');
                    $('.messageDialog').text('Tipo de Usuário adicionado com sucesso!');

                    $('table tbody').append(html); //inclui campos na tabela

                    $form.trigger("reset"); //limpa formulário

                } else {
                    //Mensagem de alerta
                    $('.alert').css('display','block');
                    $('.alert').addClass('alert-warning');
                    $('.messageDialog').text('Não foi possível adcionar!');


                    $('.kf').logger("click",function(){
                       alert('teste de validação')
                    });

                }
            }
        });

    });
});
