(function ($) {
    "use strict";
    var HT = {};

    HT.toggleStatus = function () {
        $(document).on('change', '.toggle-status', function () {
            var _this = $(this);
            var userId = _this.data('id');
            var status = _this.is(':checked') ? 1 : 2;
            $.ajax({
                url: BASE_URL + '/ajax/user/toggle-status',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: userId, status: status }),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function (data) {
                    if (!data.success) {
                        toastr.error(data.message || 'Cập nhật trạng thái thất bại!');
                        _this.prop('checked', !_this.is(':checked'));
                    } else {
                        
                        if (data.status == 1) {
                            toastr.success(data.message); 
                        } else {
                            toastr.error(data.message);   
                        }
                
                        // Cập nhật badge trạng thái
                        var $badge = _this.siblings('.badge');
                        if (data.status == 1) {
                            $badge
                                .removeClass('badge-danger')
                                .addClass('badge-success')
                                .text('Hoạt động');
                        } else {
                            $badge
                                .removeClass('badge-success')
                                .addClass('badge-danger')
                                .text('Khóa');
                        }
                    }
                },
                error: function () {
                    alert('Có lỗi xảy ra!');
                    _this.prop('checked', !_this.is(':checked'));
                }
            });
        });
    };

    $(document).ready(function () {
        HT.toggleStatus();
    });
})(jQuery);