<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phiên làm việc hết hạn - 419</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .error-icon {
            font-size: 80px;
            color: #ff6b6b;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        .error-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .btn-secondary {
            background: #f8f9fa;
            color: #6c757d;
            border: 2px solid #e9ecef;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: #e9ecef;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-clock"></i>
        </div>
        <h1 class="error-title">Phiên làm việc hết hạn</h1>
        <p class="error-message">
            Phiên làm việc của bạn đã hết hạn. Điều này xảy ra khi bạn ở trên trang quá lâu mà không có hoạt động nào.
            <br><br>
            Vui lòng làm mới trang và thử lại.
        </p>
        <div class="d-flex gap-3 justify-content-center">
            <button onclick="window.location.reload()" class="btn btn-primary">
                <i class="fas fa-refresh"></i> Làm mới trang
            </button>
            <button onclick="window.history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </button>
        </div>
    </div>

    <script>
        // Auto refresh after 10 seconds
        setTimeout(function() {
            window.location.reload();
        }, 10000);
    </script>
</body>
</html>
