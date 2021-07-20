$('thead').on('click', '.addRow', function (){
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
    $(this).parent().parent().remove();
})

$('thead').on('click', '.addPurchaseRow', function (){
    var tr = "<tr>" +
        "<td><input type='text' name='issue_date[ ]' class='form-control'></td>" +
        "<td><input type='text' name='due_date[ ]' class='form-control'></td>" +
        "<td><input type='text' name='invoice_number[ ]' class='form-control'></td>" +
        "<td><input type='text' name='NIP[ ]' id='nipId' list='companiesData' class='form-control'></td>" +
        "<datalist id='companiesData'>" +
        "                                @for($i=0; $i<count(session('companiesData')); $i++)\n" +
        "                                    <option value='{{ session(\"companiesData\")[$i][2] }}' class='form-control'></option>" +
        "                                @endfor" +
        "                            </datalist>" +
        "<td><textarea type='text' rows='1' name='company[ ]' class='form-control'></textarea></td>" +
        "<td><textarea type='text' rows='1' name='address[ ]' class='form-control'></textarea></td>" +
        "<td><input type='text' name='netto[ ]' class='form-control'></td>" +
        "<td><input type='text' name='vat[ ]' class='form-control'></td>" +
        "<td><input type='text' name='brutto[ ]' class='form-control'></td>" +
        "<th><a href='javascript:void(0)' class='btn btn-next deleteRow'>Usuń</a> </th>" +
        "</tr>"

    $('tbody').append(tr);




});

$('tbody').on('click', '.deletePurchaseRow', function (){
    $(this).parent().parent().remove();
})

$('thead').on('click', '.addLink', function (){
    var tr = "<tr id='group[ ]'>" +
        "<td><input type='text' name='link[ ]' class='form-control'></td>" +
        "<th><a href='javascript:void(0)' class='btn btn-next deleteRow'>Usuń</a> </th>" +
        "</tr>"

    $('tbody').append(tr);
});

$('tbody').on('click', '.deleteLink', function (){
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


$('tbody').on('change', 'input', function (){
    var company = $(this).parent().next("td").children();
    var address = $(this).parent().next("td").next('td').children();

    let nip = $(this).val();
    console.log(nip);
    const companies= $("#hdnSession").data('value');
    $(companies).each(function (index, value){
        if(nip === value[2]){
            company.val(value[0]);
            address.val(value[1]);

            if(value[0].length >30){
                company.attr('rows', '2');
            }
            if(value[1].length >30){
                address.attr('rows', '2');
            }

        }
    })
});

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


