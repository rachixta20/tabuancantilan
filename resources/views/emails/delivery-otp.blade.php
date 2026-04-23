<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Inter, Arial, sans-serif; background: #f9fafb; margin: 0; padding: 32px 16px; }
        .card { max-width: 480px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .header { background: #16a34a; padding: 28px 32px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; margin: 0; }
        .body { padding: 32px; }
        .otp-box { background: #f0fdf4; border: 2px dashed #86efac; border-radius: 12px; text-align: center; padding: 24px; margin: 24px 0; }
        .otp { font-size: 48px; font-weight: 800; letter-spacing: 12px; color: #15803d; font-family: monospace; }
        .info { background: #fef9c3; border-radius: 8px; padding: 12px 16px; font-size: 13px; color: #854d0e; margin-bottom: 16px; }
        p { color: #374151; font-size: 14px; line-height: 1.6; margin: 0 0 12px; }
        .footer { padding: 16px 32px; background: #f9fafb; border-top: 1px solid #f3f4f6; text-align: center; }
        .footer p { font-size: 12px; color: #9ca3af; margin: 0; }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1>🌿 TABUAN</h1>
    </div>
    <div class="body">
        <p>Hi <strong>{{ $order->buyer->name }}</strong>,</p>
        <p>Your order <strong>{{ $order->order_number }}</strong> is on its way!</p>
        <p>When the seller/rider arrives, they will ask for your <strong>Delivery Confirmation Code</strong>. Give them this code to confirm receipt:</p>

        <div class="otp-box">
            <div class="otp">{{ $order->delivery_otp }}</div>
            <p style="margin:8px 0 0;font-size:13px;color:#16a34a;font-weight:600;">Delivery Confirmation Code</p>
        </div>

        <div class="info">
            ⏰ This code expires on <strong>{{ $order->delivery_otp_expires_at->format('F d, Y \a\t h:i A') }}</strong>.
        </div>

        <p><strong>How it works:</strong></p>
        <p>1. The seller delivers your order to your address.<br>
           2. You give them this 6-digit code.<br>
           3. They enter the code in the app to confirm delivery.<br>
           4. You will have <strong>48 hours</strong> to raise a dispute if there's an issue.</p>

        <p style="color:#6b7280;font-size:13px;">Do not share this code until you have physically received your order.</p>
    </div>
    <div class="footer">
        <p>TABUAN Marketplace · Cantilan, Surigao del Sur</p>
    </div>
</div>
</body>
</html>
