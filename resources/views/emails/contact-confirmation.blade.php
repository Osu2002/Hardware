<!DOCTYPE html>
<html>
<head>
    <title>Thank You for Your Inquiry</title>
</head>
<body>
    <h2>Thank You for Contacting Us</h2>
    
    <p>Dear {{ $inquiry->name }},</p>
    
    <p>We have received your inquiry and will get back to you soon.</p>
    
    <p><strong>Your Inquiry Type:</strong></p>
    <p>{{ $inquiry->inquiry_type }}</p>
    
    <p><strong>Your Message:</strong></p>
    <p>{{ $inquiry->message }}</p>
    
    <p>If you need immediate assistance, please contact us at info@nikoba.com.</p>
    
    <p>Best regards,<br>Word Auto Dealers Team</p>
</body>
</html>