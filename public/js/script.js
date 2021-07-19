$('thead').on('click', '.addRow', function (){
    console.log('ok');
    var tr = "<tr id='group[ ]'>" +
        "<td><input type='text' name='due_date[ ]' class='form-control'></td>" +
        "<td><input type='text' name='products_names[ ]' class='form-control'></td>" +
        "<td><input type='text' name='quantity[ ]' class='form-control'></td>" +
        "<td><input type='text' name='products[ ]' class='form-control'></td>" +
        "<th><a href='javascript:void(0)' class='btn btn-next deleteRow'>Usuń</a> </th>" +
        "</tr>"

    $('tbody').append(tr);
});

$('tbody').on('click', '.deleteRow', function (){
    console.log('ok');
    $(this).parent().parent().remove();
})

$('thead').on('click', '.addPurchaseRow', function (){
    console.log('ok');
    var tr = "<tr id='group[ ]'>" +
        "<td><input type='text' name='issue_date[ ]' class='form-control'></td>" +
        "<td><input type='text' name='due_date[ ]' class='form-control'></td>" +
        "<td><input type='text' name='invoice_number[ ]' class='form-control'></td>" +
        "<td><textarea type='text' rows='1' name='company[ ]' class='form-control'></textarea></td>" +
        "<td><textarea type='text' rows='1' name='address[ ]' class='form-control'></textarea></td>" +
        "<td><input type='text' name='NIP[ ]' class='form-control'></td>" +
        "<td><input type='text' name='netto[ ]' class='form-control'></td>" +
        "<td><input type='text' name='vat[ ]' class='form-control'></td>" +
        "<td><input type='text' name='brutto[ ]' class='form-control'></td>" +
        "<th><a href='javascript:void(0)' class='btn btn-next deleteRow'>Usuń</a> </th>" +
        "</tr>"

    $('tbody').append(tr);
});

$('tbody').on('click', '.deletePurchaseRow', function (){
    console.log('ok');
    $(this).parent().parent().remove();
})

$('thead').on('click', '.addLink', function (){
    console.log('ok');
    var tr = "<tr id='group[ ]'>" +
        "<td><input type='text' name='link[ ]' class='form-control'></td>" +
        "<th><a href='javascript:void(0)' class='btn btn-next deleteRow'>Usuń</a> </th>" +
        "</tr>"

    $('tbody').append(tr);
});

$('tbody').on('click', '.deleteLink', function (){
    console.log('ok');
    $(this).parent().parent().remove();
})

$('#form').click(function (){
    $('#form').addClass("bg-gray");
    $('#file').removeClass("bg-gray");
    $('#fileContainer').addClass("visually-hidden");
    $('#formContainer').removeClass("visually-hidden");
    $('#dataOrigin').attr('name', 'formSales');

    $("input").prop('required',true);

})

$('#file').click(function (){
    $('#file').addClass("bg-gray");
    $('#form').removeClass("bg-gray");
    $('#fileContainer').removeClass("visually-hidden");
    $('#formContainer').addClass("visually-hidden");
    $('#dataOrigin').attr('name', 'fileSales');

    $("input").prop('required',true);

})

$('#companyId').change(function (){
    let nip = $('#companyId').val();
    let companies= $("#hdnSession").data('value');
    // let key = $.inArray(nip, companies);
    if(nip in companies){
        alert(companies[nip])
    } else {
        alert("This number does not exists")
    }
    // console.log(nip, companies);
})



function checkfile(sender) {
    var validExts = new Array(".csv");
    var fileExt = sender.value;
    fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
    if (validExts.indexOf(fileExt) < 0) {
        alert("Niepoprawny format plików. Obługiwane rozszerzenie to  " +
            validExts.toString());
        $("#link").val(null);
        return false;
    }
    else return true;
}


