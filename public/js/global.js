$.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

$('a').on('click', function (e) {
    if ($(this).attr('href') === '#!') {
        e.preventDefault();
    }
});

let current_url = location.href
$('.menu.menu-root a').each(function () {
    if (current_url === this.href) {
        $(this).addClass('active');
        $(this).closest('.menu-item.menu-item-group').children('a').addClass('active');
    }
});
$(function () {
    $('.navbar__body .menu-link').click(function (e) {
        $('.navbar__body .menu-link').removeClass('active');
        $(this).addClass('active');
        $(this).closest('.menu-item.menu-item-group').children('a').addClass('active');

        //in app file
        if ($(window).width() < 1200){
            $(".js-navbar-toggle").toggleClass("active");
            $(".js-navbar").toggleClass("is-show");
            $("html, body").toggleClass("overflow-hidden");
        }
    })
});
$('.js-request_for_quote').click(function (e) {
    e.preventDefault();
    let form = $(this).closest('form');
    $(this).attr({disabled: "disabled"});
    $(this).text(current_locale === 'en' ? 'Waiting...' : 'Đang gửi...');
    let _this = $(this);

    let name = form.find("input[name='name']").val();
    let email = form.find("input[name='email']").val();
    let subject = form.find("input[name='subject']").val();
    let content = form.find("textarea[name='content']").val();
    let url = $(this).data('url');

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {
            name: name,
            email: email,
            subject: subject,
            content: content
        },
        success: function (res) {
            _this.removeAttr('disabled');
            _this.text(current_locale === 'en' ? 'Send Feedback' : 'Gửi yêu cầu');
            console.log(res);
            if (res.hasOwnProperty('error')) {
                form.find('.ajax-response').html("<span class='text-danger'>" + res.error.message + "</span>");
            } else {
                form.find('.ajax-response').html("<span class='text-success'>" + res.message + "</span>");
                form.find("input[name='email']").val('');
                form.find("input[name='name']").val('');
                form.find("input[name='subject']").val('');
                form.find("textarea[name='content']").val('');
            }
        }
    });
});


$('.js-btn-recruitment-save').click(function (e) {
    e.preventDefault();
    let form = $(this).closest('form');
    $(this).attr({disabled: "disabled"});
    $(this).text(current_locale === 'en' ? 'Waiting...' : 'Đang gửi...');
    let _this = $(this);

    let name = form.find("input[name='name']").val();
    let email = form.find("input[name='email']").val();
    let phone = form.find("input[name='phone']").val();
    let profession_id = form.find("select[name='profession_id']").val();
    let content = form.find("textarea[name='content']").val();

    let url = $(this).data('url');

    var formData = new FormData();
    var files = $('#cv_file')[0].files;

    // Check file selected or not
    if (files.length > 0) {
        formData.append('cv_file', files[0]);
    }
    formData.append('name', name);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('profession_id', profession_id);
    formData.append('content', content);

    $.ajax({
        url: url,
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            _this.removeAttr('disabled');
            _this.text(current_locale === 'en' ? 'Apply' : 'Ứng tuyển');
            console.log(response);
            if (response.hasOwnProperty('error')) {
                form.find('.ajax-response').html("<span class='text-danger'>" + response.error.message + "</span>");
            } else {
                form.find('.ajax-response').html("<span class='alert alert-success d-block'>" + response.message + "</span>");
                form.find("input[name='name']").val('');
                form.find("input[name='email']").val('');
                form.find("input[name='phone']").val('');
                form.find("textarea[name='content']").val('');
            }
        },
    });
});


$('.js-load_more').click(function (e) {
    e.preventDefault();
    $(this).attr({disabled: "disabled"});
    $(this).text('Loading...');
    let _this = $(this);

    let wrap = $(this).data('wrap');
    let url = $(this).data('url');
    let page = $(this).data('page');
    let type = $(this).data('type');
    let cat_id = $(this).data('cat_id');

    $.ajax({
        url: url,
        type: 'POST',
        dataType: 'json',
        data: {
            type: type,
            page: page,
            cat_id: cat_id
        },
        success: function (res) {
            _this.removeAttr('disabled');
            _this.text('Load more');
            if (res.hasOwnProperty('error')) {
                //
            } else {
                let next_page = res.next_page;
                $('#' + wrap + ' .wrap_load_more').append(res.html);
                if (next_page === 0) {
                    _this.hide();
                }
                _this.data('page', res.next_page)
            }
        }
    });
});
