<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600&display=swap');
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    .center {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .turnstile-container {
        width: 100%;
        margin-top: 16px;
        overflow-x: auto;
    }
    form .text {
    color: #333;
    width: 100%;
    text-align: center;
    font-size: 12px;
    }
    form .text h3 a{
    color: #4070f4;
    text-decoration: none;
    }
    form .text h3 a:hover{
    text-decoration: underline;
    }
    .container form .button {
        display: relative;
        justify-content: center; /* Horizontally center */
        align-items: center;    /* Vertically center */

        height: 100%;           /* Ensure the container takes the full height */
    }
    .container form button.submit {
        margin: 3px; /* Remove any extra margin */
    }
    body {
        min-height: 100vh;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 20px 0;
        background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
            url('{{ asset('usercss/assets/img/deped1.jpg') }}') no-repeat center center/cover;
    }
    .container{
        position: relative;
        max-width: 900px;
        width: 100%;
        border-radius: 6px;
        padding: 30px;
        margin: 0 15px;
        background: rgba(255, 255, 255, 0.8); /* White background with 90% opacity */
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }
    .container header{
        position: relative;
        font-size: 20px;
        font-weight: 600;
        color: #333;
    }
    .container header::before{
        content: "";
        position: absolute;
        left: 0;
        bottom: -2px;
        height: 3px;
        width: 27px;
        border-radius: 8px;
    }
    .container form{
        position: relative;
        margin-top: 16px;
    }
    .container form .form{
        position: relative;
        width: 100%;
        transition: 0.3s ease;
    }
    .container form .form.second{
        opacity: 0;
        pointer-events: none;
        transform: translateX(100%);
    }
    form.secActive .form.second{
        opacity: 1;
        pointer-events: auto;
        transform: translateX(0);
    }
    form.secActive .form.first{
        opacity: 0;
        pointer-events: none;
        transform: translateX(-100%);
    }
    .container form .title{
        display: block;
        margin-bottom: 8px;
        font-size: 16px;
        font-weight: 500;
        margin: 6px 0;
        color: #333;
    }
    .container form .fields{
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    form .fields .input-field{
        display: flex;
        width: calc(100% / 3 - 15px);
        flex-direction: column;
        margin: 4px 0;
    }
    .input-field {
        position: relative; /* Set relative positioning for the input field container */
    }

    .input-field .error {
        color: red;
        font-size: 12px;
        position: absolute; /* Makes the error message not affect the layout */
        top: 70px; /* Positions the error message just below the input field */
        left: 0;
        width: 100%; /* Ensures the error spans the width of the input field */
        text-align: center;
    }

    .input-field input,
    .input-field select {
        outline: none;
        font-size: 14px;
        font-weight: 400;
        color: #333;
        border-radius: 5px;
        border: 1px solid #aaa;
        padding: 0 12px;
        height: 42px;
        margin: 8px 0;
        box-sizing: border-box; /* Ensures padding and border are included in the height */
    }

    .input-field input:focus,
    .input-field select:focus {
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.13);
    }

    .input-field label {
        font-size: 12px;
        font-weight: 500;
        color: #2e2e2e;
    }

    .password-hint {
        align-items: flex-start;
        background-color: rgba(0, 123, 255, 0.08);
        border-left: 3px solid #007BFF;
        border-radius: 3px;
        color: #444;
        display: flex;
        font-size: 11.5px;
        gap: 5px;
        line-height: 1.4;
        margin-top: 1px;
        padding: 6px 8px;
    }

    .password-hint i {
        color: #007BFF;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .password-fields-row {
        align-items: start;
        column-gap: 22px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        width: 100%;
    }

    .password-fields-row .input-field {
        width: 100%;
    }

    .password-input-wrap {
        position: relative;
    }

    .password-input-wrap input {
        padding-right: 38px;
        width: 100%;
    }

    .password-toggle {
        align-items: center;
        background: transparent;
        border: 0;
        color: #6c757d;
        cursor: pointer;
        display: flex;
        height: 42px;
        justify-content: center;
        margin: 8px 0;
        padding: 0;
        position: absolute;
        right: 2px;
        top: 0;
        width: 36px;
    }

    .password-field .error {
        position: static;
        margin-top: 4px;
        text-align: left;
    }

    .input-field select,
    .input-field input[type="date"] {
        color: #707070;
    }

    .input-field input[type="date"]:valid {
        color: #333;
    }

    .container form button, .backBtn{
        display: flex;
        align-items: center;
        justify-content: center;
        height: 45px;
        max-width: 200px;
        width: 100%;
        border: none;
        outline: none;
        color: #fff;
        border-radius: 5px;
        margin: 25px 0;
        background-color: #4070f4;
        transition: all 0.3s linear;
        cursor: pointer;
    }
    .container form .btnText{
        font-size: 14px;
        font-weight: 400;
    }
    form button:hover{
        background-color: #265df2;
    }
    .container form button.password-toggle,
    .container form button.password-toggle:hover {
        background: transparent;
        border: 0;
        color: #6c757d;
        height: 42px;
        margin: 8px 0;
        max-width: 36px;
        padding: 0;
        position: absolute;
        right: 2px;
        top: 0;
        width: 36px;
    }
    form button i,
    form .backBtn i{
        margin: 0 6px;
    }
    form .backBtn i{
        transform: rotate(180deg);
    }
    form .buttons{
        display: flex;
        align-items: center;
    }
    form .buttons button , .backBtn{
        margin-right: 14px;
    }

    @media (max-width: 750px) {
        form .fields .input-field{
            width: calc(100% / 2 - 15px);
        }
    }

    @media (max-width: 550px) {
        .container {
            padding: 20px 15px;
        }
        form .fields .input-field{
            width: 100%;
        }
        .password-fields-row {
            grid-template-columns: 1fr;
        }
    }
</style>


