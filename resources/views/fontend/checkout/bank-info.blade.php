@extends('fontend.layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-university me-2"></i>
                        Thông tin chuyển khoản ngân hàng
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Thông tin đơn hàng -->
                    <div class="alert alert-info">
                        <h5><i class="fas fa-shopping-cart me-2"></i>Thông tin đơn hàng</h5>
                        <p><strong>Mã đơn hàng:</strong> <span class="text-primary">{{ $order->id ?? 'DH' . time() }}</span></p>
                        <p><strong>Tổng tiền:</strong> <span class="text-danger">{{ number_format($order->tongtien ?? 0, 0, ',', '.') }}₫</span></p>
                        <p><strong>Ngày đặt:</strong> {{ $order->ngaytao ?? now()->format('d/m/Y H:i') }}</p>
                    </div>

                    <!-- Thông tin ngân hàng -->
                    <div class="bank-info">
                        <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Thông tin chuyển khoản</h5>
                        
                        <!-- QR Code Section -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h6 class="text-primary">📱 Quét mã QR để chuyển khoản</h6>
                                    <div id="qr-code-container" class="border rounded p-3 bg-white">
                                        <div id="qr-code">
                                            <div class="text-center text-muted">
                                                <div class="spinner-border text-primary mb-3" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <br><strong>Đang tạo mã QR...</strong>
                                                <br><small class="text-muted">Vui lòng chờ trong giây lát</small>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted mt-2 d-block">
                                        <strong>📱 Quét mã QR bằng app ngân hàng</strong><br>
                                        <small class="text-success">✅ Số tiền sẽ tự động điền: <span id="qr-amount">{{ number_format($order->tongtien ?? 0, 0, ',', '.') }}₫</span></small><br>
                                        <small class="text-info">💡 Hỗ trợ: Vietcombank, BIDV, Techcombank, Agribank, VPBank...</small>
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h6 class="text-success">💳 Hoặc chuyển khoản thủ công</h6>
                                    <div class="manual-transfer-info">
                                        <p><strong>Số tài khoản:</strong> <span class="copy-text" data-text="0004100035113001">0004100035113001</span> <button class="btn btn-sm btn-outline-primary copy-btn" data-text="0004100035113001"><i class="fas fa-copy"></i></button></p>
                                        <p><strong>Chủ tài khoản:</strong> <span class="copy-text" data-text="PHAN DANG DANH">PHAN DANG DANH</span> <button class="btn btn-sm btn-outline-primary copy-btn" data-text="PHAN DANG DANH"><i class="fas fa-copy"></i></button></p>
                                        <p><strong>Ngân hàng:</strong> OCB - PGD LÝ THƯỜNG KIỆT</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nội dung chuyển khoản -->
                    <div class="mt-4">
                        <h6><i class="fas fa-comment me-2"></i>Nội dung chuyển khoản</h6>
                        <div class="input-group">
                            <input type="text" class="form-control" id="transfer-content" value="THANH TOAN DON HANG {{ $order->id ?? 'DH' . time() }}" readonly>
                            <button class="btn btn-outline-primary copy-btn" data-text="THANH TOAN DON HANG {{ $order->id ?? 'DH' . time() }}">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                        <small class="text-muted">Vui lòng ghi chính xác nội dung này khi chuyển khoản</small>
                    </div>

                    <!-- Hướng dẫn -->
                    <div class="mt-4">
                        <h6><i class="fas fa-info-circle me-2"></i>Hướng dẫn thanh toán</h6>
                        <ol>
                            <li>Chuyển khoản đúng số tiền: <strong>{{ number_format($order->tongtien ?? 0, 0, ',', '.') }}₫</strong></li>
                            <li>Ghi chính xác nội dung chuyển khoản</li>
                            <li>Chụp ảnh biên lai chuyển khoản</li>
                            <li>Gửi biên lai cho shop qua Zalo/Facebook hoặc email</li>
                            <li>Chờ shop xác nhận và giao hàng</li>
                        </ol>
                    </div>

                    <!-- Liên hệ -->
                    <div class="mt-4">
                        <h6><i class="fas fa-phone me-2"></i>Liên hệ hỗ trợ</h6>
                        <p><strong>Hotline:</strong> <a href="tel:0379559690">0379559690</a></p>
                        <p><strong>Zalo:</strong> <a href="https://zalo.me/0379559690">0379559690</a></p>
                        <p><strong>Email:</strong> <a href="mailto:phandangdanh2002@gmail.com">phandangdanh2002@gmail.com</a></p>
                    </div>


                    <!-- Nút hành động -->
                    <div class="mt-4 text-center">
                        <a href="{{ route('orders.detail', $order->id ?? 1) }}" class="btn btn-primary me-2">
                            <i class="fas fa-eye me-2"></i>Xem đơn hàng
                        </a>
                        <a href="{{ route('checkout.check-payment') }}" class="btn btn-info me-2">
                            <i class="fas fa-search me-2"></i>Kiểm tra thanh toán
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Về trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bank-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid #007bff;
    margin-bottom: 20px;
}

.copy-text {
    font-family: 'Courier New', monospace;
    background: #e9ecef;
    padding: 2px 6px;
    border-radius: 4px;
}

.copy-btn {
    margin-left: 10px;
}

.copy-btn:hover {
    background-color: #007bff;
    color: white;
}

.alert-info {
    border-left: 4px solid #17a2b8;
}

.card {
    border: none;
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

/* QR Code Styles */
#qr-code-container {
    min-height: 280px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed #dee2e6;
    transition: all 0.3s ease;
    position: relative;
    overflow: visible;
    padding: 20px;
}

#qr-code {
    position: relative;
    z-index: 10;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

#qr-code canvas {
    position: relative !important;
    z-index: 10 !important;
    visibility: visible !important;
    opacity: 1 !important;
    display: block !important;
}

#qr-code-container:hover {
    border-color: #007bff;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
}

#qr-code {
    text-align: center;
}

#qr-code canvas {
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.manual-transfer-info {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    border-left: 4px solid #28a745;
}

.manual-transfer-info p {
    margin-bottom: 8px;
    font-size: 14px;
}

.manual-transfer-info .copy-text {
    font-family: 'Courier New', monospace;
    background: #e9ecef;
    padding: 2px 6px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.manual-transfer-info .copy-text:hover {
    background: #dee2e6;
}

</style>

<!-- Try Multiple QR Code Libraries -->
<script>
// Function to try loading QR code library from different sources
function loadQRCodeLibrary() {
    const sources = [
        'https://unpkg.com/qrcode@1.5.3/build/qrcode.min.js',
        'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/qrcode/1.5.3/qrcode.min.js'
    ];
    
    let currentIndex = 0;
    
    function tryLoad() {
        if (currentIndex >= sources.length) {
            console.log('All CDN sources failed, trying Google Charts API...');
            createGoogleQRCode();
            return;
        }
        
        const script = document.createElement('script');
        script.src = sources[currentIndex];
        script.onload = function() {
            console.log(`QR library loaded from: ${sources[currentIndex]}`);
            setTimeout(() => {
                if (typeof QRCode !== 'undefined') {
                    createRealQRCode();
                } else {
                    console.log('QRCode not available, trying next source...');
                    currentIndex++;
                    tryLoad();
                }
            }, 100);
        };
        script.onerror = function() {
            console.log(`Failed to load from: ${sources[currentIndex]}`);
            currentIndex++;
            tryLoad();
        };
        
        document.head.appendChild(script);
    }
    
    tryLoad();
}

// Create real QR code using loaded library
function createRealQRCode() {
    console.log('Creating real QR code with library...');
    
    const qrContainer = document.getElementById('qr-code');
    if (!qrContainer) return;
    
    // Thông tin đơn hàng
    const orderId = '{{ $order->id ?? "DH" . time() }}';
    const amount = {{ $order->tongtien ?? 0 }};
    const accountNumber = '0004100035113001';
    const accountName = 'PHAN DANG DANH';
    const transferContent = `THANH TOAN DON HANG ${orderId}`;
    
    // Tạo QR content đơn giản
    const qrContent = `${accountNumber}|${accountName}|${amount}|${transferContent}`;
    
    console.log('QR Content:', qrContent);
    
    const canvas = document.createElement('canvas');
    canvas.width = 200;
    canvas.height = 200;
    
    // Style canvas
    canvas.style.border = '2px solid #dee2e6';
    canvas.style.borderRadius = '10px';
    canvas.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
    canvas.style.display = 'block';
    canvas.style.margin = '0 auto';
    
    QRCode.toCanvas(canvas, qrContent, {
        width: 200,
        height: 200,
        color: {
            dark: '#000000',
            light: '#FFFFFF'
        },
        margin: 2,
        errorCorrectionLevel: 'M'
    }, function (error) {
        if (error) {
            console.error('QR Code generation error:', error);
            createSimpleQRCode();
        } else {
            console.log('Real QR code generated successfully!');
            qrContainer.innerHTML = '';
            qrContainer.appendChild(canvas);
        }
    });
}

// Create QR code using Google Charts API
function createGoogleQRCode() {
    console.log('Creating QR code with Google Charts API...');
    
    const qrContainer = document.getElementById('qr-code');
    if (!qrContainer) return;
    
    // Thông tin đơn hàng
    const orderId = '{{ $order->id ?? "DH" . time() }}';
    const amount = {{ $order->tongtien ?? 0 }};
    const accountNumber = '0004100035113001';
    const accountName = 'PHAN DANG DANH';
    const transferContent = `THANH TOAN DON HANG ${orderId}`;
    
    // Tạo QR content cho banking apps
    // Format 1: VietQR format
    const vietQRContent = `bank://transfer?account=${accountNumber}&name=${encodeURIComponent(accountName)}&amount=${amount}&content=${encodeURIComponent(transferContent)}&bank=OCB`;
    
    // Format 2: Simple format cho Zalo Pay và banking apps
    const simpleContent = `${accountNumber}|${accountName}|${amount}|${transferContent}`;
    
    // Format 3: URL format
    const urlContent = `https://banking.vn/transfer?acc=${accountNumber}&name=${encodeURIComponent(accountName)}&amount=${amount}&note=${encodeURIComponent(transferContent)}`;
    
    console.log('Available QR formats:');
    console.log('1. VietQR:', vietQRContent);
    console.log('2. Simple:', simpleContent);
    console.log('3. URL:', urlContent);
    
    // Sử dụng format đơn giản nhất để đảm bảo tương thích
    const qrContent = simpleContent;
    
    console.log('Using QR Content:', qrContent);
    
    // Tạo URL cho Google Charts QR Code
    const encodedContent = encodeURIComponent(qrContent);
    const qrUrl = `https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=${encodedContent}&choe=UTF-8&chld=M|2`;
    
    console.log('Google Charts URL:', qrUrl);
    
    // Tạo img element
    const img = document.createElement('img');
    img.src = qrUrl;
    img.alt = 'QR Code';
    img.style.border = '2px solid #dee2e6';
    img.style.borderRadius = '10px';
    img.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
    img.style.display = 'block';
    img.style.margin = '0 auto';
    img.style.width = '250px';
    img.style.height = '250px';
    img.style.backgroundColor = '#ffffff';
    
    img.onload = function() {
        console.log('Google Charts QR code loaded successfully!');
        qrContainer.innerHTML = '';
        qrContainer.appendChild(img);
        
        // Thêm thông tin debug
        console.log('QR Code created with content:', qrContent);
        console.log('Amount:', amount.toLocaleString() + '₫');
        console.log('Account:', accountNumber);
        console.log('Account Name:', accountName);
    };
    
    img.onerror = function() {
        console.log('Google Charts QR code failed, trying QR Server API...');
        createQRServerQRCode();
    };
    
    // Thêm timeout để đảm bảo load
    setTimeout(() => {
        if (!img.complete || img.naturalHeight === 0) {
            console.log('QR code loading timeout, trying QR Server API...');
            createQRServerQRCode();
        }
    }, 3000);
}

// Create QR code using QR Server API
function createQRServerQRCode() {
    console.log('Creating QR code with QR Server API...');
    
    const qrContainer = document.getElementById('qr-code');
    if (!qrContainer) return;
    
    // Thông tin đơn hàng
    const orderId = '{{ $order->id ?? "DH" . time() }}';
    const amount = {{ $order->tongtien ?? 0 }};
    const accountNumber = '0004100035113001';
    const accountName = 'PHAN DANG DANH';
    const transferContent = `THANH TOAN DON HANG ${orderId}`;
    
    // Tạo QR content đơn giản
    const qrContent = `${accountNumber}|${accountName}|${amount}|${transferContent}`;
    
    console.log('QR Content for QR Server:', qrContent);
    
    // Tạo URL cho QR Server API
    const encodedContent = encodeURIComponent(qrContent);
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodedContent}&format=png&ecc=M&margin=10`;
    
    console.log('QR Server URL:', qrUrl);
    
    // Tạo img element
    const img = document.createElement('img');
    img.src = qrUrl;
    img.alt = 'QR Code';
    img.style.border = '2px solid #dee2e6';
    img.style.borderRadius = '10px';
    img.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
    img.style.display = 'block';
    img.style.margin = '0 auto';
    img.style.width = '250px';
    img.style.height = '250px';
    img.style.backgroundColor = '#ffffff';
    
    img.onload = function() {
        console.log('QR Server QR code loaded successfully!');
        qrContainer.innerHTML = '';
        qrContainer.appendChild(img);
        
        // Thêm thông tin debug
        console.log('QR Code created with content:', qrContent);
        console.log('Amount:', amount.toLocaleString() + '₫');
        console.log('Account:', accountNumber);
        console.log('Account Name:', accountName);
    };
    
    img.onerror = function() {
        console.log('QR Server QR code failed, trying alternative API...');
        createAlternativeQRCode();
    };
    
    // Thêm timeout để đảm bảo load
    setTimeout(() => {
        if (!img.complete || img.naturalHeight === 0) {
            console.log('QR Server timeout, trying alternative API...');
            createAlternativeQRCode();
        }
    }, 3000);
}

// Create QR code using alternative API
function createAlternativeQRCode() {
    console.log('Creating QR code with alternative API...');
    
    const qrContainer = document.getElementById('qr-code');
    if (!qrContainer) return;
    
    // Thông tin đơn hàng
    const orderId = '{{ $order->id ?? "DH" . time() }}';
    const amount = {{ $order->tongtien ?? 0 }};
    const accountNumber = '0004100035113001';
    const accountName = 'PHAN DANG DANH';
    const transferContent = `THANH TOAN DON HANG ${orderId}`;
    
    // Tạo QR content đơn giản
    const qrContent = `${accountNumber}|${accountName}|${amount}|${transferContent}`;
    
    console.log('QR Content for Alternative API:', qrContent);
    
    // Tạo URL cho alternative API
    const encodedContent = encodeURIComponent(qrContent);
    const qrUrl = `https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=${encodedContent}&choe=UTF-8`;
    
    console.log('Alternative API URL:', qrUrl);
    
    // Tạo img element
    const img = document.createElement('img');
    img.src = qrUrl;
    img.alt = 'QR Code';
    img.style.border = '2px solid #dee2e6';
    img.style.borderRadius = '10px';
    img.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
    img.style.display = 'block';
    img.style.margin = '0 auto';
    img.style.width = '250px';
    img.style.height = '250px';
    img.style.backgroundColor = '#ffffff';
    
    img.onload = function() {
        console.log('Alternative API QR code loaded successfully!');
        qrContainer.innerHTML = '';
        qrContainer.appendChild(img);
        
        // Thêm thông tin debug
        console.log('QR Code created with content:', qrContent);
        console.log('Amount:', amount.toLocaleString() + '₫');
        console.log('Account:', accountNumber);
        console.log('Account Name:', accountName);
    };
    
    img.onerror = function() {
        console.log('All QR APIs failed, creating simple fallback...');
        createSimpleQRCode();
    };
    
    // Thêm timeout để đảm bảo load
    setTimeout(() => {
        if (!img.complete || img.naturalHeight === 0) {
            console.log('Alternative API timeout, creating simple fallback...');
            createSimpleQRCode();
        }
    }, 3000);
}

// Initialize QR Code immediately
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing QR code...');
    // Skip CDN loading, go straight to Google Charts API
    createGoogleQRCode();
});

// Main Banking QR Code Generator
function createOfflineQRCode() {
    console.log('Creating banking QR code...');
    
    const qrContainer = document.getElementById('qr-code');
    if (!qrContainer) {
        console.error('QR container not found!');
        return;
    }
    
    // Thông tin đơn hàng
    const orderId = '{{ $order->id ?? "DH" . time() }}';
    const amount = {{ $order->tongtien ?? 0 }};
    const accountNumber = '0004100035113001';
    const accountName = 'PHAN DANG DANH';
    const bankName = 'OCB';
    const transferContent = `THANH TOAN DON HANG ${orderId}`;
    
    // Tạo VietQR format cho banking apps
    const vietQRData = createVietQRData({
        accountNumber: accountNumber,
        accountName: accountName,
        amount: amount,
        content: transferContent,
        bankCode: 'OCB'
    });
    
    console.log('VietQR Data:', vietQRData);
    
    try {
        // Tạo canvas element
        const canvas = document.createElement('canvas');
        canvas.width = 200;
        canvas.height = 200;
        
        // Style canvas
        canvas.style.border = '2px solid #dee2e6';
        canvas.style.borderRadius = '10px';
        canvas.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        canvas.style.display = 'block';
        canvas.style.margin = '0 auto';
        canvas.style.visibility = 'visible';
        canvas.style.opacity = '1';
        canvas.style.position = 'relative';
        canvas.style.zIndex = '10';
        
        // Generate QR code using embedded library
        QRCode.toCanvas(canvas, vietQRData, {
            width: 200,
            height: 200,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            },
            margin: 2,
            errorCorrectionLevel: 'M'
        }, function (error) {
            if (error) {
                console.error('QR Code generation error:', error);
                createHTMLQRCode();
            } else {
                console.log('Real QR code generated successfully!');
                
                // Clear container và append canvas
                qrContainer.innerHTML = '';
                qrContainer.appendChild(canvas);
                
                console.log('Banking QR code created successfully!');
                console.log('Amount will auto-fill:', amount.toLocaleString() + '₫');
                console.log('QR Data:', vietQRData);
            }
        });
        
    } catch (error) {
        console.error('Error creating banking QR code:', error);
        // Fallback to HTML version
        createHTMLQRCode();
    }
}

// Tạo VietQR format data
function createVietQRData(data) {
    // Format 1: VietQR chuẩn
    const vietQR = `bank://transfer?account=${data.accountNumber}&name=${encodeURIComponent(data.accountName)}&amount=${data.amount}&content=${encodeURIComponent(data.content)}&bank=${data.bankCode}`;
    
    // Format 2: Simple banking format (dễ đọc hơn)
    const simpleFormat = `${data.accountNumber}|${data.accountName}|${data.amount}|${data.content}`;
    
    // Format 3: URL format
    const urlFormat = `https://banking.vn/transfer?acc=${data.accountNumber}&name=${encodeURIComponent(data.accountName)}&amount=${data.amount}&note=${encodeURIComponent(data.content)}`;
    
    // Format 4: Plain text format
    const plainFormat = `Chuyển khoản ${data.amount}₫ đến ${data.accountName} (${data.accountNumber}) - ${data.content}`;
    
    console.log('Available QR formats:');
    console.log('1. VietQR:', vietQR);
    console.log('2. Simple:', simpleFormat);
    console.log('3. URL:', urlFormat);
    console.log('4. Plain:', plainFormat);
    
    // Thử format đơn giản nhất trước
    return simpleFormat;
}

// Tạo EMV QR Code chuẩn quốc tế
function createEMVQRCode(data) {
    // EMV QR Code format cho chuyển khoản
    const payload = [
        '000201', // Payload Format Indicator
        '0102', // Point of Initiation Method
        '38570010A000000727012700069704070108' + data.accountNumber, // Merchant Account Information
        '5303704', // Transaction Currency (VND)
        '5802VN', // Country Code
        '6207' + data.content.substring(0, 25), // Additional Data Field Template
        '6304' + generateCRC16(data.accountNumber + data.amount.toString())
    ].join('');
    
    return payload;
}

// CRC16 calculation for QR code
function generateCRC16(data) {
    let crc = 0xFFFF;
    for (let i = 0; i < data.length; i++) {
        crc ^= data.charCodeAt(i);
        for (let j = 0; j < 8; j++) {
            if (crc & 1) {
                crc = (crc >> 1) ^ 0x8408;
            } else {
                crc >>= 1;
            }
        }
    }
    return (crc ^ 0xFFFF).toString(16).toUpperCase().padStart(4, '0');
}

// Simple QR Code generator as fallback
function createSimpleQRCode() {
    console.log('Creating simple QR code fallback...');
    
    const qrContainer = document.getElementById('qr-code');
    console.log('QR container:', qrContainer);
    
    if (!qrContainer) {
        console.error('QR container not found! Trying alternative selectors...');
        const altContainer = document.querySelector('#qr-code-container');
        console.log('Alternative container:', altContainer);
        if (!altContainer) {
            console.error('No QR container found at all!');
            return;
        }
        // Use alternative container
        const newDiv = document.createElement('div');
        newDiv.id = 'qr-code';
        altContainer.appendChild(newDiv);
        qrContainer = newDiv;
    }
    
    // Tạo nội dung QR Code cho chuyển khoản
    const orderId = '{{ $order->id ?? "DH" . time() }}';
    const amount = {{ $order->tongtien ?? 0 }};
    const accountNumber = '0004100035113001';
    const accountName = 'PHAN DANG DANH';
    const bankName = 'OCB';
    const transferContent = `THANH TOAN DON HANG ${orderId}`;
    
    console.log('Creating canvas with data:', { orderId, amount, accountNumber, accountName });
    
    // Tạo canvas đơn giản với text
    const canvas = document.createElement('canvas');
    canvas.width = 200;
    canvas.height = 200;
    canvas.style.border = '2px solid #dee2e6';
    canvas.style.borderRadius = '10px';
    canvas.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
    canvas.style.display = 'block';
    canvas.style.margin = '0 auto';
    canvas.style.visibility = 'visible';
    canvas.style.opacity = '1';
    canvas.style.position = 'relative';
    canvas.style.zIndex = '10';
    
    console.log('Canvas created:', canvas);
    
    const ctx = canvas.getContext('2d');
    
    // Vẽ background
    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, 200, 200);
    
    // Vẽ border
    ctx.strokeStyle = '#dee2e6';
    ctx.lineWidth = 2;
    ctx.strokeRect(1, 1, 198, 198);
    
    // Vẽ text thông tin
    ctx.fillStyle = '#000000';
    ctx.font = '12px Arial';
    ctx.textAlign = 'center';
    
    const lines = [
        'QR Code',
        'Chuyển khoản',
        `TK: ${accountNumber}`,
        `Chủ TK: ${accountName}`,
        `Ngân hàng: ${bankName}`,
        `Số tiền: ${amount.toLocaleString()}₫`,
        `Nội dung: ${transferContent}`
    ];
    
    let y = 30;
    lines.forEach(line => {
        ctx.fillText(line, 100, y);
        y += 20;
    });
    
    // Thêm icon QR đơn giản
    ctx.fillStyle = '#007bff';
    ctx.font = 'bold 24px Arial';
    ctx.fillText('QR', 100, 180);
    
    console.log('Canvas drawn, appending to container...');
    
    // Clear container và append canvas
    qrContainer.innerHTML = '';
    qrContainer.appendChild(canvas);
    
    console.log('Simple QR code created successfully');
    console.log('Container children:', qrContainer.children.length);
    console.log('Canvas in DOM:', document.querySelector('canvas'));
    
    // Debug canvas visibility
    setTimeout(() => {
        const canvasInDOM = document.querySelector('canvas');
        if (canvasInDOM) {
            console.log('Canvas visibility check:');
            console.log('- offsetWidth:', canvasInDOM.offsetWidth);
            console.log('- offsetHeight:', canvasInDOM.offsetHeight);
            console.log('- clientWidth:', canvasInDOM.clientWidth);
            console.log('- clientHeight:', canvasInDOM.clientHeight);
            console.log('- computed style display:', window.getComputedStyle(canvasInDOM).display);
            console.log('- computed style visibility:', window.getComputedStyle(canvasInDOM).visibility);
            console.log('- computed style opacity:', window.getComputedStyle(canvasInDOM).opacity);
            console.log('- parent container:', canvasInDOM.parentElement);
            console.log('- parent display:', window.getComputedStyle(canvasInDOM.parentElement).display);
        }
        
        // Backup: Nếu canvas không hiển thị, tạo HTML version
        if (!canvasInDOM || canvasInDOM.offsetHeight === 0) {
            console.log('Canvas not visible, creating HTML fallback...');
            createHTMLQRCode();
        }
    }, 1000);
}

// HTML-based QR Code fallback
function createHTMLQRCode() {
    console.log('Creating HTML QR code fallback...');
    
    const qrContainer = document.getElementById('qr-code');
    if (!qrContainer) return;
    
    const orderId = '{{ $order->id ?? "DH" . time() }}';
    const amount = {{ $order->tongtien ?? 0 }};
    const accountNumber = '0004100035113001';
    const accountName = 'PHAN DANG DANH';
    const bankName = 'OCB';
    const transferContent = `THANH TOAN DON HANG ${orderId}`;
    
    qrContainer.innerHTML = `
        <div class="qr-code-html" style="
            width: 200px; 
            height: 200px; 
            border: 2px solid #dee2e6; 
            border-radius: 10px; 
            background: white; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 10px;
            margin: 0 auto;
        ">
            <div style="font-size: 14px; font-weight: bold; color: #007bff; margin-bottom: 10px;">QR CODE</div>
            <div style="font-size: 10px; line-height: 1.2; color: #333;">
                <div><strong>Chuyển khoản</strong></div>
                <div>TK: ${accountNumber}</div>
                <div>Chủ TK: ${accountName}</div>
                <div>Ngân hàng: ${bankName}</div>
                <div>Số tiền: ${amount.toLocaleString()}₫</div>
                <div style="font-size: 8px; margin-top: 5px;">${transferContent}</div>
            </div>
            <div style="font-size: 20px; font-weight: bold; color: #007bff; margin-top: 10px;">QR</div>
        </div>
    `;
    
    console.log('HTML QR code created successfully');
}

</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing copy functionality...');
    
    // Copy functionality
    document.querySelectorAll('.copy-btn').forEach(button => {
        button.addEventListener('click', function() {
            const text = this.getAttribute('data-text');
            navigator.clipboard.writeText(text).then(function() {
                // Show success message
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> Copied!';
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-success');
                
                setTimeout(function() {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-primary');
                }, 2000);
            }).catch(function(err) {
                console.error('Copy failed:', err);
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                
                // Show success message
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> Copied!';
                button.classList.remove('btn-outline-primary');
                button.classList.add('btn-success');
                
                setTimeout(function() {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-primary');
                }, 2000);
            });
        });
    });
});

function showQRCodeError(message) {
    const qrContainer = document.getElementById('qr-code');
    qrContainer.innerHTML = `
        <div class="text-center text-muted">
            <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
            <br>${message}
            <br><small class="text-muted mt-2">Vui lòng sử dụng thông tin chuyển khoản thủ công bên dưới</small>
        </div>
    `;
}

function generateQRCode() {
    console.log('=== Starting QR Code Generation ===');
    
    // Kiểm tra lại QRCode library
    if (typeof QRCode === 'undefined') {
        console.error('QRCode library is still undefined!');
        showQRCodeError('Thư viện QR Code chưa được tải. Vui lòng tải lại trang.');
        return;
    }
    
    console.log('QRCode library is available:', typeof QRCode);
    
    // Tạo nội dung QR Code cho chuyển khoản
    const orderId = '{{ $order->id ?? "DH" . time() }}';
    const amount = {{ $order->tongtien ?? 0 }};
    const accountNumber = '0004100035113001'; // OCB account number
    const accountName = 'PHAN DANG DANH'; // OCB account name
    const bankName = 'OCB';
    const transferContent = `THANH TOAN DON HANG ${orderId}`;
    
    // Tạo QR Code content đơn giản
    const qrContent = `Chuyển khoản:
Số TK: ${accountNumber}
Chủ TK: ${accountName}
Ngân hàng: ${bankName}
Số tiền: ${amount.toLocaleString()}₫
Nội dung: ${transferContent}`;
    
    console.log('Order ID:', orderId);
    console.log('Amount:', amount);
    console.log('QR Content:', qrContent);
    
    // Tạo QR Code
    const qrContainer = document.getElementById('qr-code');
    
    if (!qrContainer) {
        console.error('QR container not found!');
        return;
    }
    
    console.log('QR container found:', qrContainer);
    
    // Clear container trước
    qrContainer.innerHTML = '';
    
    try {
        console.log('Calling QRCode.toCanvas...');
    
    QRCode.toCanvas(qrContainer, qrContent, {
        width: 200,
        height: 200,
        color: {
            dark: '#000000',
            light: '#FFFFFF'
        },
        margin: 2,
        errorCorrectionLevel: 'M'
    }, function (error) {
        if (error) {
            console.error('QR Code generation error:', error);
                showQRCodeError('Không thể tạo QR Code: ' + error.message);
        } else {
                console.log('QR Code generated successfully!');
            // Thêm border cho canvas
            const canvas = qrContainer.querySelector('canvas');
            if (canvas) {
                canvas.style.border = '2px solid #dee2e6';
                canvas.style.borderRadius = '10px';
                    canvas.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                    console.log('Canvas styled successfully');
                } else {
                    console.warn('Canvas not found after generation');
                }
            }
        });
    } catch (error) {
        console.error('QR Code generation exception:', error);
        showQRCodeError('Lỗi khi tạo QR Code: ' + error.message);
        }
}
</script>
@endsection
