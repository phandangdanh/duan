// jQuery required
$(function () {
    // Thiết lập header CSRF cho mọi request jQuery
    var csrf = $('meta[name="csrf-token"]').attr('content');
    if (csrf) {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': csrf }
        });
    }

    $(document).on('change', '.toggle-status-sanpham', function () {
        var $cb = $(this);
        var url = $cb.data('url');
        var status = $cb.is(':checked') ? 1 : 0;
        var $control = $cb.closest('.status-control');
        var $label = $control.find('.status-btn');
        var $text = $label.find('.status-text');
        var $row = $cb.closest('tr');

        $('.loading-overlay').show();

        $.ajax({
            url: url,
            type: 'POST',
            data: { status: status },
            dataType: 'json'
        }).done(function (res) {
            if (res && res.success) {
                if (status) {
                    $label.removeClass('inactive').addClass('active');
                    $text.text('Kinh doanh');
                    $row.removeClass('status-inactive-san-pham').addClass('status-active-san-pham');
                } else {
                    $label.removeClass('active').addClass('inactive');
                    $text.text('Không kinh doanh');
                    $row.removeClass('status-active-san-pham').addClass('status-inactive-san-pham');
                }
                if (window.toastr) toastr.success(res.message || 'Cập nhật trạng thái thành công');
            } else {
                $cb.prop('checked', !status);
                if (window.toastr) toastr.error(res?.message || 'Có lỗi xảy ra khi cập nhật trạng thái');
            }
        }).fail(function (xhr) {
            $cb.prop('checked', !status);
            var msg = 'Có lỗi xảy ra khi cập nhật trạng thái';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            if (window.toastr) toastr.error(msg);
            console.error('Toggle status error:', xhr);
        }).always(function () {
            $('.loading-overlay').hide();
        });
    });
});

