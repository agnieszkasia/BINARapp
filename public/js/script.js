$('thead').on('click', '.addRow', function (){
    console.log('ok');
    var tr = "<tr id='group[ ]'>" +
        "<td><input type='text' name='due_date[ ]' class='form-control'></td>" +
        "<td><input type='text' name='products_names[ ]' class='form-control'></td>" +
        "<td><input type='text' name='products[ ]' class='form-control'></td>" +
        "<th><a href='javascript:void(0)' class='btn btn-danger deleteRow'>Usuń</a> </th>" +
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
        "<th><a href='javascript:void(0)' class='btn btn-danger deleteRow'>Usuń</a> </th>" +
        "</tr>"

    $('tbody').append(tr);
});

$('tbody').on('click', '.deletePurchaseRow', function (){
    console.log('ok');
    $(this).parent().parent().remove();
})
