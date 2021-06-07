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
