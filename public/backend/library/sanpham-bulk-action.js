// jQuery required
$(function () {
    // global CSRF header (nếu chưa set)
    var csrf = $('meta[name="csrf-token"]').attr('content');
    if (csrf) $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': csrf } });

    function gatherSelectedIds() {
        return $('.select-item:checked').map(function () { return $(this).val(); }).get();
    }

    function applyUpdateToRow(id, statusOrDeleted) {
        var $row = $('input.select-item[value="' + id + '"]').closest('tr');
        if (! $row.length) return;
        if (statusOrDeleted === 'deleted') {
            $row.fadeOut(200, function () { $(this).remove(); });
            return;
        }
        // update row class
        $row.removeClass('status-active-san-pham status-inactive-san-pham')
            .addClass(statusOrDeleted ? 'status-active-san-pham' : 'status-inactive-san-pham');

        // update toggle checkbox and label in row
        var $cb = $row.find('.toggle-status-sanpham');
        var $label = $row.find('.status-btn');
        var $text = $row.find('.status-text');

        if ($cb.length) $cb.prop('checked', !!statusOrDeleted);
        if ($label.length) {
            if (statusOrDeleted) {
                $label.removeClass('inactive').addClass('active');
                $text.text('Kinh doanh');
            } else {
                $label.removeClass('active').addClass('inactive');
                $text.text('Không kinh doanh');
            }
        }
    }

    // handle any of the bulk forms (they post to same route)
    $('#bulk-activate-form, #bulk-deactivate-form, #bulk-delete-form').on('submit', function (e) {
        e.preventDefault();
        var $form = $(this);
        var action = $form.find('input[name="action"]').val();
        var idsInput = $form.find('input[name="ids"]');
        var idsVal = idsInput.val();
        var ids = [];

        if (idsVal && idsVal.trim().length) {
            ids = idsVal.split(',').filter(Boolean);
        } else {
            ids = gatherSelectedIds();
            if (ids.length) idsInput.val(ids.join(','));
        }

        if (!ids.length) {
            alert('Vui lòng chọn ít nhất 1 sản phẩm.');
            return;
        }

        $('.loading-overlay').show();

        $.post($form.attr('action'), { action: action, ids: ids.join(',') })
            .done(function (res) {
                if (res && res.success) {
                    // cập nhật UI theo response.updated hoặc tự đoán
                    if (res.updated) {
                        Object.keys(res.updated).forEach(function (id) {
                            applyUpdateToRow(id, res.updated[id]);
                        });
                    } else {
                        // fallback: apply same status to all selected
                        var newStatus = action === 'activate' ? 1 : (action === 'deactivate' ? 0 : 'deleted');
                        ids.forEach(function (id) { applyUpdateToRow(id, newStatus); });
                    }
                    if (window.toastr) toastr.success(res.message || 'Thành công');
                    // reset selection
                    $('#select-all').prop('checked', false);
                    $('.select-item').prop('checked', false);
                    $('#selected-count').text(0);
                    $('#bulk-ids-activate, #bulk-ids-deactivate, #bulk-ids-delete').val('');
                } else {
                    if (window.toastr) toastr.error(res.message || 'Thao tác thất bại');
                }
            })
            .fail(function (xhr) {
                var msg = 'Có lỗi xảy ra';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (window.toastr) toastr.error(msg);
            })
            .always(function () {
                $('.loading-overlay').hide();
            });
    });
});