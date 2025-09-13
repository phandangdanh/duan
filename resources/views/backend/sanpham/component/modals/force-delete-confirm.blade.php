<!-- Force Delete Confirmation Modal -->
<div class="modal fade" id="forceDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-exclamation-triangle text-danger"></i> Xác nhận xóa vĩnh viễn
                </h5>
                <!-- Support both Bootstrap 4 and 5 -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-left:8px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa vĩnh viễn sản phẩm <strong id="force-delete-product-name"></strong>?</p>
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Cảnh báo:</strong> Hành động này KHÔNG THỂ hoàn tác! Tất cả dữ liệu liên quan sẽ bị xóa vĩnh viễn khỏi database.
                </div>
                <div class="alert alert-warning">
                    <i class="fa fa-info-circle"></i>
                    <strong>Lưu ý:</strong> Hình ảnh, chi tiết sản phẩm, bình luận và đánh giá liên quan cũng sẽ bị xóa vĩnh viễn.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-force-delete">
                    <i class="fa fa-trash-o"></i> Xóa vĩnh viễn
                </button>
            </div>
        </div>
    </div>
</div>
