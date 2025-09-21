<h1>Chào mừng bạn đến với {{ $shopName }}!</h1>

<p>Xin chào {{ $realName }},</p>

<p>Cảm ơn bạn đã đăng ký tài khoản tại cửa hàng của chúng tôi. Dưới đây là thông tin đăng nhập của bạn:</p>

<ul>
    <li><strong>Tên đăng nhập:</strong> {{ $userName }}</li>
    <li><strong>Mật khẩu:</strong> {{ $password }}</li>
</ul>

<p>Bạn có thể đăng nhập vào tài khoản của mình <a href="{{ route('login') }}">tại đây</a>.</p>

<p>Nếu bạn có bất kỳ câu hỏi nào, xin vui lòng liên hệ với chúng tôi.</p>

<p>Trân trọng,</p>
<p>{{ $shopName }}</p>
