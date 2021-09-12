<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="x-apple-disable-message-reformatting">
        
        <style>
            table, td, div, h1, h6, p {
                font-family: Arial, sans-serif;
            }

            
            .btn a {
                text-decoration: none;
                color: #ffffff;
                background-color: #217d7e;
                text-align: center;
                font-size: 14px;
                font-weight: bold;
                padding: 5px 50px 5px 50px;
                border-radius: 15px;
            }
        </style>
    </head>

    <body style="margin:0;padding:0;">
        <table role="presentation" style="width:602px; border-collapse:collapse; border:1px solid #217d7e; border-spacing:0; text-align:left;">
            <!-- Header Content  -->
            <tr>
                <td style="padding:5px 15px 5px 15px; background:#217d7e;">
                    <h3 style="color:#ffffff;">Amadeus Marine Ltd. - Drawing Database</h3>
                </td>
            </tr>

            <!-- Body Content  -->
            <tr>
                <table role="presentation" style="width:100%; border-collapse:collapse; border:0; border-spacing:0;">
                    <tr>
                        <td style="padding-left:15px;">
                            <h3>Hi {{ $name }},</h3>
                            <p>{{ $note }} You may now start using web-app with the following credentials:</p>
                        </td>
                    </tr>

                    <tr>
                        <table>
                            <tr>
                                <td style="padding-left:15px;">Email: <b>{{ $email }}</b></td>
                            </tr>

                            <tr>
                                <td style="padding-left:15px;">Temporary Password: <b>{{ $password }}</b></td>
                            </tr>
                        </table>
                    </tr>

                    <tr>
                        <td style="padding-left:15px;">
                            <p>As a safety precaution, kindly change your password upon your login.</p>
                        </td>
                    </tr>

                    <tr>
                        <td  class="btn" style="padding-left:15px;">
                            <a href="{{ $link }}" class="btn">Login</a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-left:15px;">
                            <p>This is an automated message. Please do not reply directly to this email.</p>
                        </td>
                    </tr>
                </table>
                
            </tr>

            <!-- Footer Content  -->
            <tr>
                <td style="padding:5px 15px 5px 15px; background:#217d7e;">
                    <p style="color:#ffffff;">&copy; 2021 - Amadeus Marine Ltd.</p>
                </td>
            </tr>
        </table>
    </body>

</html>