<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Verifikasi Akun</title>        
    </head>
    
    <body>
        <p>
            Halo <b>{{ $details['name'] }}</b>!
        </p>

        <p>
            Anda telah melakukan registrasi akun di website Bank Majaya dengan menggunakan email ini.
        </p>

        <p>
           Berikut adalah data diri anda:
        </p>

        <table>
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ $details['name'] }}</td>
            </tr>
            <tr>
                <td>Nomor Identitas</td>
                <td>:</td>
                <td>{{ $details['no_identitas'] }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $details['alamat'] }}</td>
            </tr>
        </table>

        <p>
            Terima kasih Anda telah melakukan registrasi!.
        </p>

    </body>
</html>