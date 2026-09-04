<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Transaction Authorization - NovaTrust Bank</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            color: #333;
        }


        /* ============================== */
        /* NAVBAR */
        /* ============================== */

        .navbar {
            background-color: #1a237e;
            color: #fff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .logo {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .navbar .menu a,
        .navbar .menu button {

            color: #fff;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;

            background: none;
            border: none;

            cursor: pointer;

            font-family: inherit;
            font-size: 15px;
        }

        .navbar .menu a:hover,
        .navbar .menu button:hover {
            text-decoration: underline;
        }


        /* ============================== */
        /* MAIN CONTAINER */
        /* ============================== */

        .container {

            max-width: 500px;

            margin: 50px auto;

            background: #fff;

            border-radius: 10px;

            box-shadow:
                0 3px 8px rgba(0, 0, 0, 0.1);

            padding: 35px 30px;
        }


        /* ============================== */
        /* HEADER */
        /* ============================== */

        .security-icon {

            width: 65px;
            height: 65px;

            margin: 0 auto 15px;

            background: #e8eaf6;

            border-radius: 50%;

            display: flex;

            justify-content: center;
            align-items: center;

            font-size: 30px;
        }


        h2 {

            text-align: center;

            color: #1a237e;

            margin-bottom: 8px;

            font-weight: 600;
        }


        .subtitle {

            text-align: center;

            color: #777;

            font-size: 14px;

            line-height: 1.6;

            margin-bottom: 25px;
        }


        /* ============================== */
        /* ALERTS */
        /* ============================== */

        .alert {

            padding: 12px;

            border-radius: 6px;

            text-align: center;

            margin-bottom: 20px;

            font-size: 14px;
        }


        .alert.success {

            background-color: #e8f5e9;

            color: #2e7d32;

            border: 1px solid #c8e6c9;
        }


        .alert.error {

            background-color: #ffebee;

            color: #c62828;

            border: 1px solid #ffcdd2;
        }


        /* ============================== */
        /* TRANSACTION BOX */
        /* ============================== */

        .transaction-box {

            background: #f8f9fc;

            border: 1px solid #e2e5ec;

            border-radius: 8px;

            padding: 18px;

            margin-bottom: 25px;
        }


        .transaction-box h3 {

            margin-top: 0;

            margin-bottom: 15px;

            color: #1a237e;

            font-size: 16px;
        }


        .transaction-row {

            display: flex;

            justify-content: space-between;

            padding: 9px 0;

            border-bottom: 1px solid #e5e5e5;

            font-size: 14px;
        }


        .transaction-row:last-child {

            border-bottom: none;
        }


        .transaction-label {

            color: #777;
        }


        .transaction-value {

            font-weight: 600;

            color: #333;

            text-align: right;
        }


        .amount-value {

            color: #1a237e;

            font-size: 16px;

            font-weight: bold;
        }


        /* ============================== */
        /* TAC INPUT */
        /* ============================== */

        .tac-label {

            display: block;

            text-align: center;

            font-weight: bold;

            margin-bottom: 12px;

            color: #333;
        }


        .tac-input-container {

            display: flex;

            justify-content: center;

            gap: 8px;

            margin-bottom: 20px;
        }


        .tac-digit {

            width: 48px;

            height: 55px;

            text-align: center;

            font-size: 22px;

            font-weight: bold;

            border: 1px solid #ccc;

            border-radius: 7px;

            outline: none;

            transition: 0.2s;
        }


        .tac-digit:focus {

            border-color: #1a237e;

            box-shadow: 0 0 0 2px rgba(26, 35, 126, 0.1);
        }


        /* ============================== */
        /* EXPIRATION */
        /* ============================== */

        .expiration {

            text-align: center;

            margin-bottom: 22px;

            font-size: 14px;

            color: #777;
        }


        #countdown {

            font-weight: bold;

            color: #c62828;
        }


        /* ============================== */
        /* BUTTON */
        /* ============================== */

        button[type="submit"] {

            background-color: #1a237e;

            color: #fff;

            border: none;

            width: 100%;

            padding: 13px;

            border-radius: 6px;

            font-size: 16px;

            font-weight: 600;

            cursor: pointer;

            transition: 0.2s;
        }


        button[type="submit"]:hover {

            background-color: #0d1b63;
        }


        button[type="submit"]:disabled {

            background: #888;

            cursor: not-allowed;
        }


        /* ============================== */
        /* CANCEL BUTTON */
        /* ============================== */

        .cancel-button {

            display: block;

            width: 100%;

            text-align: center;

            margin-top: 15px;

            padding: 11px;

            border-radius: 6px;

            border: 1px solid #ddd;

            text-decoration: none;

            color: #555;

            font-weight: 600;

            font-size: 14px;
        }


        .cancel-button:hover {

            background: #f5f5f5;
        }


        /* ============================== */
        /* SECURITY MESSAGE */
        /* ============================== */

        .security-message {

            text-align: center;

            font-size: 12px;

            color: #888;

            margin-top: 20px;

            line-height: 1.6;
        }


        /* ============================== */
        /* PROCESSING OVERLAY */
        /* ============================== */

        #processingOverlay {

            position: fixed;

            top: 0;
            left: 0;

            width: 100%;
            height: 100%;

            background: rgba(0, 0, 0, 0.65);

            display: none;

            z-index: 999999;

            justify-content: center;
            align-items: center;

            flex-direction: column;

            color: white;

            font-size: 20px;

            font-weight: bold;
        }


        .spinner {

            border: 6px solid rgba(255,255,255,0.3);

            border-top: 6px solid #ffffff;

            border-radius: 50%;

            width: 60px;
            height: 60px;

            animation: spin 1s linear infinite;

            margin-bottom: 15px;
        }


        @keyframes spin {

            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }


        /* ============================== */
        /* MOBILE */
        /* ============================== */

        @media (max-width: 600px) {

            .navbar {

                padding: 15px;

                flex-direction: column;

                gap: 15px;
            }


            .navbar .menu {

                display: flex;

                flex-wrap: wrap;

                justify-content: center;

                gap: 10px;
            }


            .navbar .menu a,
            .navbar .menu button {

                margin-left: 0;
            }


            .container {

                margin: 30px 15px;

                padding: 30px 20px;
            }


            .tac-digit {

                width: 42px;

                height: 52px;
            }

        }

    </style>

</head>


<body>


<!-- =============================== -->
<!-- PROCESSING OVERLAY -->
<!-- =============================== -->

<div id="processingOverlay">

    <div class="spinner"></div>

    Verifying TAC Code...

</div>



<!-- =============================== -->
<!-- NAVBAR -->
<!-- =============================== -->

<div class="navbar">

    <div class="logo">

        NovaTrust Bank

    </div>


    <div class="menu">

        <a href="/dashboard">

            Dashboard

        </a>


        <a href="/transfer">

            Transfer

        </a>


        <a href="/history">

            History

        </a>


        <form action="{{ route('logout') }}"

              method="POST"

              style="display:inline;">

            @csrf

            <button type="submit">

                Logout

            </button>

        </form>

    </div>

</div>



<!-- =============================== -->
<!-- MAIN CONTENT -->
<!-- =============================== -->

<div class="container">


    <!-- SECURITY ICON -->

    <div class="security-icon">

        🔒

    </div>


    <h2>

        Transaction Authorization

    </h2>


    <p class="subtitle">

        Please enter your Transaction Authorization Code (TAC)
        to authorize this transaction.

    </p>



    <!-- =============================== -->
    <!-- SUCCESS MESSAGE -->
    <!-- =============================== -->

    @if(session('success'))

        <div class="alert success">

            {{ session('success') }}

        </div>

    @endif



    <!-- =============================== -->
    <!-- ERROR MESSAGE -->
    <!-- =============================== -->

    @if(session('error'))

        <div class="alert error">

            {{ session('error') }}

        </div>

    @endif



    <!-- =============================== -->
    <!-- VALIDATION ERRORS -->
    <!-- =============================== -->

    @if($errors->any())

        <div class="alert error">

            @foreach($errors->all() as $error)

                <div>

                    {{ $error }}

                </div>

            @endforeach

        </div>

    @endif



    <!-- =============================== -->
    <!-- TRANSACTION DETAILS -->
    <!-- =============================== -->

    <div class="transaction-box">


        <h3>

            Transaction Details

        </h3>



        <div class="transaction-row">

            <span class="transaction-label">

                Account Number

            </span>


            <span class="transaction-value">

                {{ session('transfer.account_number', '********') }}

            </span>

        </div>



        <div class="transaction-row">

            <span class="transaction-label">

                Account Name

            </span>


            <span class="transaction-value">

                {{ session('transfer.account_name', 'Pending') }}

            </span>

        </div>



        <div class="transaction-row">

            <span class="transaction-label">

                Bank

            </span>


            <span class="transaction-value">

                {{ session('transfer.bank_name', 'Pending') }}

            </span>

        </div>



        <div class="transaction-row">

            <span class="transaction-label">

                Amount

            </span>


            <span class="transaction-value amount-value">

                ${{ number_format((float) session('transfer.amount', 0), 2) }}

            </span>

        </div>


    </div>



    <!-- =============================== -->
    <!-- TAC FORM -->
    <!-- =============================== -->

    <form

        action="{{ route('tac.verify') }}"

        method="POST"

        id="tacForm"

    >

        @csrf



        <label class="tac-label">

            Enter 6-Digit TAC Code

        </label>



        <!-- HIDDEN FINAL TAC -->

        <input

            type="hidden"

            name="tac_code"

            id="tac_code"

        >



        <!-- TAC DIGITS -->

        <div class="tac-input-container">


            <input

                type="text"

                maxlength="1"

                class="tac-digit"

                inputmode="numeric"

                autocomplete="one-time-code"

                required

            >


            <input

                type="text"

                maxlength="1"

                class="tac-digit"

                inputmode="numeric"

                required

            >


            <input

                type="text"

                maxlength="1"

                class="tac-digit"

                inputmode="numeric"

                required

            >


            <input

                type="text"

                maxlength="1"

                class="tac-digit"

                inputmode="numeric"

                required

            >


            <input

                type="text"

                maxlength="1"

                class="tac-digit"

                inputmode="numeric"

                required

            >


            <input

                type="text"

                maxlength="1"

                class="tac-digit"

                inputmode="numeric"

                required

            >


        </div>



        <!-- COUNTDOWN -->

        <div class="expiration">

            TAC Code expires in:

            <span id="countdown">

                05:00

            </span>

        </div>



        <!-- AUTHORIZE BUTTON -->

        <button

            type="submit"

            id="authorizeButton"

        >

            Authorize Transfer

        </button>


    </form>



    <!-- CANCEL -->

    <a

        href="/transfer"

        class="cancel-button"

    >

        Cancel Transaction

    </a>



    <!-- SECURITY MESSAGE -->

    <div class="security-message">

        🔒 For your security, never share your
        Transaction Authorization Code with anyone.

    </div>


</div>



<!-- =============================== -->
<!-- FLOATING CHAT BUTTON -->
<!-- =============================== -->

<a

    href="{{ route('user.chat') }}"

    id="floatingChatBtn"

>

    Chat


    <span

        id="unread-badge"

        class="chat-notify-bubble"

    >

        0

    </span>

</a>



<style>


#floatingChatBtn {

    position: fixed;

    bottom: 25px;

    right: 25px;

    width: 70px;

    height: 70px;

    background: #28a745;

    color: white;

    font-size: 16px;

    font-weight: bold;

    border-radius: 50%;

    display: flex;

    justify-content: center;

    align-items: center;

    box-shadow: 0 4px 14px rgba(0,0,0,0.28);

    cursor: pointer;

    z-index: 9999;

    animation: floatPulse 1.8s infinite;

    text-decoration: none;

}


#floatingChatBtn:hover {

    background: #1e7e34;

}


@keyframes floatPulse {

    0% {

        transform: translateY(0px);

    }


    50% {

        transform: translateY(-4px);

    }


    100% {

        transform: translateY(0px);

    }

}


.chat-notify-bubble {

    position: absolute;

    top: 6px;

    right: 6px;

    background: red;

    color: white;

    font-size: 11px;

    padding: 2px 6px;

    border-radius: 50%;

    font-weight: bold;

    display: none;

}

</style>



<!-- =============================== -->
<!-- JAVASCRIPT -->
<!-- =============================== -->

<script>


/* ============================== */
/* TAC INPUT SYSTEM */
/* ============================== */


const tacDigits = document.querySelectorAll('.tac-digit');


tacDigits.forEach((input, index) => {


    input.addEventListener('input', function () {


        /* Allow numbers only */

        this.value = this.value.replace(/[^0-9]/g, '');


        /* Move to next box */

        if (this.value.length === 1) {


            if (index < tacDigits.length - 1) {

                tacDigits[index + 1].focus();

            }

        }


        updateTacCode();

    });



    input.addEventListener('keydown', function (event) {


        /* Move backward */

        if (

            event.key === 'Backspace'

            &&

            this.value === ''

            &&

            index > 0

        ) {

            tacDigits[index - 1].focus();

        }


    });


});


/* ============================== */
/* COMBINE TAC DIGITS */
/* ============================== */


function updateTacCode() {


    let code = '';


    tacDigits.forEach(function (input) {

        code += input.value;

    });


    document.getElementById('tac_code').value = code;

}



/* ============================== */
/* PASTE TAC CODE */
/* ============================== */


tacDigits[0].addEventListener('paste', function (event) {


    event.preventDefault();


    const pastedData =

        event.clipboardData

        .getData('text')

        .replace(/[^0-9]/g, '')

        .substring(0, 6);


    pastedData

        .split('')

        .forEach(function (number, index) {


            if (tacDigits[index]) {

                tacDigits[index].value = number;

            }


        });


    updateTacCode();


    if (pastedData.length === 6) {

        tacDigits[5].focus();

    }


});



/* ============================== */
/* FORM SUBMIT */
/* ============================== */


const tacForm =

    document.getElementById('tacForm');


const processingOverlay =

    document.getElementById('processingOverlay');


const authorizeButton =

    document.getElementById('authorizeButton');



tacForm.addEventListener(

    'submit',

    function (event) {


        updateTacCode();


        const tacCode =

            document.getElementById('tac_code').value;


        if (tacCode.length !== 6) {


            event.preventDefault();


            alert(

                'Please enter your complete 6-digit TAC Code.'

            );


            return;

        }



        processingOverlay.style.display = 'flex';


        authorizeButton.disabled = true;


        authorizeButton.textContent =

            'Verifying...';


    }

);



/* ============================== */
/* COUNTDOWN TIMER */
/* UI ONLY - BACKEND MUST VERIFY */
/* ============================== */


let timeRemaining = 300;


const countdownElement =

    document.getElementById('countdown');



const countdownInterval =

    setInterval(

        function () {


            let minutes =

                Math.floor(timeRemaining / 60);


            let seconds =

                timeRemaining % 60;


            seconds =

                seconds < 10

                ?

                '0' + seconds

                :

                seconds;


            countdownElement.textContent =

                minutes

                +

                ':'

                +

                seconds;



            if (timeRemaining <= 0) {


                clearInterval(

                    countdownInterval

                );


                countdownElement.textContent =

                    'EXPIRED';


                authorizeButton.disabled = true;


                tacDigits.forEach(

                    function (input) {


                        input.disabled = true;


                    }

                );


                alert(

                    'This TAC Code has expired.'

                );


            }


            timeRemaining--;


        },

        1000

    );



/* ============================== */
/* UNREAD CHAT COUNT */
/* ============================== */


function loadUnreadCount() {


    fetch(

        "{{ route('messages.unread.count') }}"

    )


    .then(

        response => response.json()

    )


    .then(

        data => {


            const badge =

                document.getElementById(

                    'unread-badge'

                );


            if (!badge) return;



            if (data.count > 0) {


                badge.innerText =

                    data.count;


                badge.style.display =

                    'inline-block';


            }

            else {


                badge.style.display =

                    'none';


            }


        }

    );


}


loadUnreadCount();


setInterval(

    loadUnreadCount,

    5000

);


</script>


</body>

</html>
