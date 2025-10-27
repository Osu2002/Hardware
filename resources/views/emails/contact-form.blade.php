<!DOCTYPE html>
<html lang="en">
<head>
  <title>Lead Notification</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      background-color: #ffffff;
    }

    a[x-apple-data-detectors] {
      color: inherit !important;
      text-decoration: inherit !important;
    }

    #MessageViewBody a {
      color: inherit;
      text-decoration: none;
    }

    p {
      line-height: 1.5;
    }

    .desktop_hide {
      display: none;
      max-height: 0;
      overflow: hidden;
    }

    .image_block img+div {
      display: none;
    }

    sup, sub {
      font-size: 75%;
      line-height: 0;
    }

    .lead-details {
      width: 100%;
      font-family: Arial, Helvetica, sans-serif;
      font-size: 16px;
      color: #101112;
      margin: 20px 0;
      margin-left: 100px; /* Aligns left edge of titles with logo image */
    }

    .lead-item {
      display: flex;
      justify-content: flex-start;
      align-items: center;
      padding: 12px 0;
      border-bottom: 1px solid #e0e0e0;
    }

    .lead-title {
      flex: 1;
      text-align: left;
      font-weight: bold;
      padding: 12px 10px 12px 20px; /* Consistent padding for 'P' alignment */
      color: #0b0d40;
      max-width: 200px; /* Ensure consistent width for alignment */
    }

    .lead-colon {
      width: 20px;
      text-align: center;
      padding: 12px 0;
      color: #101112;
    }

    .lead-value {
      flex: 1;
      text-align: left;
      padding: 12px 10px;
      color: #101112;
    }

    .logo-img {
      border-radius: 10px;
      overflow: hidden;
    }

    .header-section {
      text-align: center;
    }

    @media (max-width: 520px) {
      .row-content {
        width: 100% !important;
      }

      .stack .column {
        width: 100%;
        display: block;
      }

      .desktop_hide {
        display: table !important;
        max-height: none !important;
      }

      .lead-details {
        margin-left: 0; /* Reset margin for mobile */
      }

      .lead-item {
        flex-direction: column;
        align-items: center;
      }

      .lead-title,
      .lead-colon,
      .lead-value {
        width: 100%;
        text-align: center;
        padding: 8px 15px;
      }

      .lead-title {
        padding: 8px 15px; /* Reset padding for mobile */
        max-width: none; /* Remove max-width on mobile */
      }

      .logo-img {
        border-radius: 8px;
      }
    }
  </style>
</head>

<body style="background-color: #14233412; margin: 0; padding: 0;">
  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #ffffff;">
    <tr>
      <td>
        <table align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color: #f7f7f7;">
          <tr>
            <td>
              <table align="center" cellpadding="0" cellspacing="0" role="presentation" style="width: 600px; margin: 0 auto;" width="600">
                <tr>
                  <td style="padding: 20px 0;">
                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                      <tr>
                        <td align="center" style="padding: 20px 0;">
                          <div style="max-width: 400px;">
                            <div class="logo-img">
                              <img src="https://play-lh.googleusercontent.com/0iZnDRkCMzcK6ZxLbg83jSlR_GZHLCPc32sfNt01lfMRUqnOyKW9qKE_-64YnSpq3A" style="display: block; width: 100%; height: auto;" alt="Logo">
                            </div>
                          </div>
                        </td>
                      </tr>
                    </table>
                    <table width="100%" cellpadding="10" cellspacing="0" role="presentation" class="header-section">
                      <tr>
                        <td>
                          <h1 style="margin: 0; font-size: 28px; font-family: Arial, Helvetica, sans-serif; color: #0b0d40;">Hi, Administrator</h1>
                        </td>
                      </tr>
                    </table>
                    <table width="100%" cellpadding="10" cellspacing="0" role="presentation" class="header-section">
                      <tr>
                        <td>
                          <p style="font-size: 16px; font-family: Arial, Helvetica, sans-serif; color: #101112; margin: 10px 0;">We have an incoming lead for you.</p>
                          <p style="font-size: 16px; font-family: Arial, Helvetica, sans-serif; color: #101112;">See the details of the lead below.</p>
                        </td>
                      </tr>
                    </table>

                    <!-- LEAD DETAILS -->
                    <div class="lead-details">
                      <div class="lead-item">
                        <div class="lead-title">Name</div>
                        <div class="lead-colon">: </div>
                        <div class="lead-value">{{ $contact->name}}</div>
                      </div>
                      <div class="lead-item">
                        <div class="lead-title">Email</div>
                        <div class="lead-colon">: </div>
                        <div class="lead-value">{{ $contact->email}}</div>
                      </div>
                      <div class="lead-item">
                        <div class="lead-title">Phone Number</div>
                        <div class="lead-colon">: </div>
                        <div class="lead-value">{{ $contact->phone}}</div>
                      </div>
                      <div class="lead-item">
                        <div class="lead-title">Inquiry</div>
                        <div class="lead-colon">: </div>
                        <div class="lead-value">{{ $contact->enquiry_type}}</div>
                      </div>
                      <div class="lead-item">
                        <div class="lead-title">Message</div>
                        <div class="lead-colon">: </div>
                        <div class="lead-value">{{ $contact->enquiry}}</div>
                      </div>
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>