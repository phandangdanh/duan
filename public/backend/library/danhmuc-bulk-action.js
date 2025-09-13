(function ($) {
    "use strict";
    var HT = {};

    // Loại bỏ toggle status function vì đã được xử lý trong table.blade.php
    // HT.toggleStatus = function () {
    //     $(document).on('change', '.toggle-status-danhmuc', function () {
    //         var _this = $(this);
    //         var userId = _this.data('id');
    //         var status = _this.is(':checked') ? 1 : 0; // Laravel dùng 1 = hoạt động, 0 = khóa

    //         $.ajax({
    //             url: BASE_URL + '/ajax/danhmuc/toggle-status-danhmuc',
    //             type: 'POST',
    //             data: {
    //                 id: userId,
    //                 status: status,
    //                 _token: $('meta[name="csrf-token"]').attr('content') // thêm CSRF vào đây
    //             },
    //             dataType: 'json',
    //             success: function (data) {
    //                 if (!data.success) {
    //                     toastr.error(data.message || 'Cập nhật trạng thái thất bại!');
    //                     _this.prop('checked', !_this.is(':checked'));
    //                 } else {
    //                     toastr.success(data.message);

    //                     // // Cập nhật badge trạng thái
    //                     // var $badge = _this.closest('td').find('.badge');
    //                     // if (data.status == 1) {
    //                     //     $badge
    //                     //         .removeClass('danger')
    //                     //         .addClass('badge-success')
    //                     //         .text('Hoạt động'); // xanh
    //                     // } else {
    //                     //     $badge
    //                     //         .removeClass('badge-success')
    //                     //         .addClass('danger')
    //                     //         .text('Vô hiệu hóa'); // đỏ
    //                     // }
    //                 }
    //             },
    //             error: function () {
    //                 alert('Có lỗi xảy ra!');
    //                 _this.prop('checked', !_this.is(':checked'));
    //             }
    //         });
    //     });
    // };

    $(document).ready(function () {
        // HT.toggleStatus(); // Đã loại bỏ vì xung đột với table.blade.php
    });
})(jQuery);
