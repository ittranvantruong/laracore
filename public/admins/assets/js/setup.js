
var token = jQuery('meta[name="csrf-token"]').attr('content'), 
urlHome = jQuery('meta[name="url-home"]').attr('content'),
currency = jQuery('meta[name="currency"]').attr('content'),
positionCurrency = jQuery('meta[name="position_currency"]').attr('content'),
columns;

function generateUID(prefix = '') {
    return prefix + Math.floor(Math.random() * 26) + Date.now();
}

function number_format(number, decimals, dec_point, thousands_sep) {
    // *     example: number_format(1234.56, 2, ',', ' ');
    // *     return: '1 234,56'
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
function searchColumsDataTable(datatable, column_search = [], column_date = [], column_select = [], column_select2 = [] ) {
    datatable.api().columns(column_search).every(function () {
        
        var column = this, 
        input = document.createElement("input"),
        findColumnSelect, findColumnSelect2
        input.setAttribute('class', 'form-control'), 
        flagColSelect2Ajax = false;

        if(column_date.length > 0 && column_date.indexOf(column.selector.cols) !== -1){

            input.setAttribute('type', 'date');

        }else if(findColumnSelect = column_select.find(obj => obj.column === column.selector.cols)){

            input = document.createElement("select");
            createSelectColumnUniqueDatatableAll(input, findColumnSelect.data);

        }else if(findColumnSelect2 = column_select2.find(obj => obj.column === column.selector.cols)){

            var resultColumnSelect2 = $.grep(column_select2, function(element) {
                return element.column === column.selector.cols;
            });

            if (resultColumnSelect2.length > 0) {
                input = document.createElement("select");
                if (findColumnSelect2.ajax === true && findColumnSelect2.url) {
                    flagColSelect2Ajax = true;
                    input.setAttribute('class', 'form-select select2-bs5-ajax-many');
                    input.setAttribute('multiple', 'true');
                    input.setAttribute('data-url', findColumnSelect2.url);
                } else {
                    createSelect2ColumnDatatable(input, findColumnSelect2.data);
                }
            }
            
        }

        input.setAttribute('placeholder', window.__trans('enterKeyword'));

        $(input).appendTo($(column.footer()).empty())
        .on('change', function () {
            column.search($(this).val(), false, false, true).draw();
        });
    }); 
}
function formatPrice(price = 0){
    price = number_format(price, 0, ',', ',');
    return positionCurrency == 'left' ? currency + price : price + currency;
}
function addSelect2(elm = '.select2-bs5'){
    if($(elm).length){
        $(elm).select2({
            placeholder: window.__trans('choose'),
            language: "vi",
            theme: 'bootstrap-5',
            allowClear: true
        });
    }
}

function select2LoadDataMany(target = '.select2-bs5-ajax-many'){
    var elm = $(target);
    if(elm.length > 0){
        elm.each(function () { 
            select2LoadData('', this);
        });
    }
}

function select2LoadData(url = '', target = '.select2-bs5-ajax'){
    if($(target).length > 0){
        if(!url){
            url = $(target).data('url');
        }
    
        $(target).select2({
            placeholder: window.__trans('choose'),
            language: "vi",
            theme: 'bootstrap-5',
            allowClear: true,
            ajax: {
                delay: 250,  // wait 250 milliseconds before triggering the request
                url: url,
                dataType: 'json',
                processResults: function (data, params) {
                    return data;
                }
            }
        });
    }
}
function createSelectColumnUniqueDatatable(column, input){
    var optionAll = document.createElement("OPTION");
    optionAll.text = window.__trans('all');
    optionAll.value = '';
    input.setAttribute('class', 'form-select');
    input.append(optionAll);

    column.data().unique().sort().each(function(d, j) {
        var option = document.createElement("OPTION");
        option.value = option.text = d;
        input.append(option);
    });
}

function addWrapTableScroll(idTable){
    $(idTable).wrap('<div class="wrap-table-scroll"></div>');
}

function moveSearchColumnsDatatable(idTable){
    $(idTable + ' thead').append($(idTable + ' tfoot tr'));
}
function createSelect2ColumnDatatable(input, data){
    input.setAttribute('class', 'form-select select2-bs5');
    input.setAttribute('multiple', 'true');

    if(typeof data === 'object'){
        Object.keys(data).map((index) => {
            var option = document.createElement("OPTION");
            $.each(data[index], function(key, value) {
                option.value = key;
                option.text = value;
            });
            input.append(option);
        });
    }else{
        data.forEach(function(value, index) {
            var option = document.createElement("OPTION");
            option.value = option.text = value;
            input.append(option);
        });
    }
}
function createSelectColumnUniqueDatatableAll(input, data){
    var optionAll = document.createElement("OPTION");
    optionAll.text = window.__trans('all');
    optionAll.value = '';
    input.setAttribute('class', 'form-select');
    input.append(optionAll);
    if(typeof data === 'object'){
        Object.keys(data).map((key) => {
            var option = document.createElement("OPTION");
            option.value = key;
            option.text = data[key];
            input.append(option);
        });
    }else{
        data.forEach(function(value, index) {
            var option = document.createElement("OPTION");
            option.value = option.text = value;
            input.append(option);
        });
    }
}

function toggleColumnsDatatable(columns){
	var headerColumns = columns.header().map(d => d.textContent).toArray(), 
    htmlToggleColumns = '', checked;
    $.each(headerColumns, function( index, value ){
        checked = '';
        if(columns.column(index).visible() === true){
            checked = 'checked';
        }
        htmlToggleColumns += `
            <label class="dropdown-item"><input class="toggle-vis form-check-input m-0 me-2" ${checked} type="checkbox" data-column="${index}">${value}</label>
        `;
        $(".drop-toggle-columns").html(htmlToggleColumns);
    });
}

function msgSuccess(text) {
    $.toast({
        heading: window.__trans('success'),
        text: text,
        position: 'top-right',
        icon: 'success',
        hideAfter: 5000
    });
}

function msgError(text) {
    $.toast({
        heading: window.__trans('fail'),
        text: text,
        position: 'top-right',
        icon: 'error',
        hideAfter: 10000
    });
}

function msgWarning(text) {
    $.toast({
        heading: window.__trans('warning'),
        text: text,
        position: 'top-right',
        icon: 'warning',
        hideAfter: 10000
    });
}

function handleAjaxError(errors) {
    if (errors.status == 416 || errors.status == 422) {
        $.map(errors.responseJSON.errors, function(value) {
            value.forEach(element => {
                msgError(element);
            })
        })
    } else {
        msgError(window.__trans('pleaseReload'));
    }

}

function selectImageCKFinder( preview, in_value, type ) {
	CKFinder.popup( {
		chooseFiles: true,
		width: 800,
		height: 600,
		onInit: function( finder ) {

			finder.on( 'files:choose', function( evt ) {

				if(type == 'MULTIPLE'){
					var files = evt.data.files;

				    var html = '', url_file;
				    var value = $(in_value).val() ? $(in_value).val()+',' : '' ;
				    files.forEach( function( file, i ) {
						url_file = file.getUrl().replace(urlHome, ''); 
				    	html += `<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 mt-3">
                                    <span data-route="0" data-url="${url_file}" class="delete-image-ckfinder">
                                        <i class="ti ti-x"></i>
                                    </span>
                                    <img src="${file.getUrl()}" width="100%">
                                </div>`;
						if(i < files.length - 1){
							value += url_file + ',';
						}else{
							value += url_file;
						}
				    } );
				    $(preview).append(html);
				    $(in_value).val(value);
				}
				else{
                    var file = evt.data.files.first();
					$(preview).attr('src', file.getUrl());
					$(in_value).val(file.getUrl().replace(urlHome, ''));
				}
			} );
		}
		
	} );
	
}

function selectFileCKFinder( in_value ) {
	CKFinder.popup( {
		chooseFiles: true,
		width: 800,
		height: 600,
		onInit: function( finder ) {

			finder.on( 'files:choose', function( evt ) {

                var file = evt.data.files.first();
                $(in_value).val(file.getUrl().replace(urlHome, '')).trigger("change");

			} );
		}
		
	} );
}
function deleteItemGallery(that, input) {
	var url = that.data('url'), 
		url_file = input.val().replace(url, ''); 
        
	if(url_file.indexOf(',,') !== -1) {
		url_file = url_file.replace(',,', ',');	
	}
	if(url_file.indexOf(',') == 0) {
		url_file = url_file.slice(1);	
	}
	if(url_file.lastIndexOf(',') == url_file.length - 1) {
		url_file = url_file.slice(0, -1);	
	}
	input.val(url_file);

}

function endAjax(element, text){

    element = element.find('button[type="submit"]');
    element.removeAttr('disabled');
    element.html(text);
    
    // $('.select2-selection__rendered').empty();
}
function removeOverlayLoading(elm){
    elm = $(elm);
    elm.find('#overlayLoading').remove();
    elm.find("button[type='submit']").css("opacity", "1");
    elm.find("button[type='submit'] .spinner-grow").remove();
}
function addOverlayLoading(elm){
    elm = $(elm);
    elm.prepend('<div id="overlayLoading" style="position: absolute;width: 100%;height: 100%;background: #ffffff91;z-index: 10;"></div>')
    elm.find("button[type='submit']").css("opacity", "0.5");
    elm.find("button[type='submit']").prepend('<span class="spinner-grow spinner-grow-sm"></span>');
}
$(document).ready(function () {
    
    select2LoadDataMany();

    $("form").submit(function(){
		addOverlayLoading(this);
    });
    var currentLocation = window.location.href; // Lấy đường dẫn của trang hiện tại
    // Duyệt qua từng phần tử li trong menu
    $("#sidebar-menu li").each(function() {
        var menuItem = $(this);
        var menuLink = menuItem.find("a");
        $(menuLink).each(function() {
            linkLocation = $(this).attr('href');
            // So sánh đường dẫn của menu item với đường dẫn của trang hiện tại
            if (linkLocation === currentLocation) {
                $(this).addClass('active');
                menuItem.find(".dropdown-toggle.nav-link, .dropdown-menu").addClass("show");
                // menuItem.find(".dropdown-toggle.nav-link").addClass("show");
            }
        });
    });
    if($("#blockSubmit").length){
        $(window).scroll(function() {

            var scrollTop = $(window).scrollTop();
    
            if (scrollTop >= $("#blockSubmit").offset().top + $("#blockSubmit").height()) {
                $("#blockSubmitFixed").css('display', 'block');
            }else{
                $("#blockSubmitFixed").css('display', 'none');
            }
        });
    }
});
$(document).on('click', '.add-image-ckfinder', function(e){
    selectImageCKFinder($(this).data('preview'), $(this).data('input'), $(this).data('type'));
});

$(document).on('click', '.add-file-ckfinder', function(e){
    selectFileCKFinder($(this).data('input'));
});

//envent toggle columns datatables
$(document).on('change', 'input.toggle-vis', function (e) {
	e.preventDefault();
	
	// Get the column API object
	var column = columns.column($(this).attr('data-column'));
	// Toggle the visibility
	column.visible(!column.visible());
	addSelect2();
	select2LoadDataMany();
});

//thông báo lỗi khi chưa chọn bản ghi để xử lý
$(document).on('submit', '#formMultiple', function(e) {

	if($('.check-list:checked').length == 0){
		e.preventDefault();
        $.toast({
            heading: window.__trans('notìy'),
            text: window.__trans('pleaseChooseRecord'),
            position: 'top-right',
            icon: 'warning'
        });
        endAjax($(this), window.__trans('apply'));
		return;
    }
	if(!confirm('Bạn có muốn thực hiện?')){
		e.preventDefault();
		endAjax($(this), window.__trans('apply'));
		return;
	}
})

//check all
$(document).on('click', '.check-all', function(e) {
    $(".check-list").prop('checked', $(this).prop('checked'));
    if($(this).prop('checked') == true){
        $('.check-all').prop('checked', true);
        $(".select-action-multiple").removeAttr('style');
    }
    else{
        $('.check-all').prop('checked', false);
        $(".select-action-multiple").css('display', 'none');
    }
});

$(document).on('click', '.check-list', function(e) {
    if($(this).prop('checked') == false){
        $('.check-all').prop('checked', false);
    }
    if($('.check-list:checked').length == $('.check-list').length){
        $('.check-all').prop('checked', true);
    }
    if($('.check-list:checked').length > 0){
        $(".select-action-multiple").removeAttr('style');
    }else{
        $(".select-action-multiple").css('display', 'none');
    }
});

$(document).on('click', '.open-modal-delete', function () {
    var form = $("#modalFormDelete"), action = $(this).data('route');
    form.attr('action', action);
});

$(document).on('click', '.open-modal-success', function () {
    var form = $("#modalFormSuccess"), action = $(this).data('route');
    form.attr('action', action);
});

$(document).on('click', '.open-modal-warning', function () {
    var form = $("#modalFormWarning"), action = $(this).data('route');
    form.attr('action', action);
});

$(document).on('click', '.delete-image-ckfinder', function(e) {
	if(!confirm(window.__trans('alertConfirm'))){
		return;
	}
	var that = $(this),
	input = $(that.parents('.wrap-ckfinder-multiple').find('input'));
	
	deleteItemGallery(that, input);

	that.parent().remove();
});

function deleteItemGallery(that, input) {
	var url = that.data('url'), 
		url_file = input.val().replace(url, ''); 
        
	if(url_file.indexOf(',,') !== -1) {
		url_file = url_file.replace(',,', ',');	
	}
	if(url_file.indexOf(',') == 0) {
		url_file = url_file.slice(1);	
	}
	if(url_file.lastIndexOf(',') == url_file.length - 1) {
		url_file = url_file.slice(0, -1);	
	}
	input.val(url_file);

}