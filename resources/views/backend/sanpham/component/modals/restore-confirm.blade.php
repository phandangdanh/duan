<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-undo text-warning"></i> Xác nhận phục hồi
                </h5>
                <!-- Support both Bootstrap 4 and 5 -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-left:8px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn phục hồi sản phẩm <strong id="restore-product-name"></strong>?</p>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>Lưu ý:</strong> Khi phục hồi, tất cả dữ liệu liên quan (hình ảnh, chi tiết, bình luận) cũng sẽ được phục hồi.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-warning" id="confirm-restore">
                    <i class="fa fa-undo"></i> Phục hồi
                </button>
            </div>
        </div>
    </div>
</div>
