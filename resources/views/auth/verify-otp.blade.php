<style>
    .otp-input-group input.otp-digit {
        width: 45px;
        height: 50px;
        font-size: 24px;
        border-radius: 6px;
        border: 1px solid #ccc;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .otp-input-group input.otp-digit:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
        outline: none;
    }

    .otp-input-group {
        gap: 10px;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .otp-digit {
        width: 50px;
        height: 50px;
        font-size: 24px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 6px;
    }
</style>


@extends('layouts.app')

@section('content')
    <script>
        window.onload = function() {
            $('#otpModal').modal('show');
        };

        document.addEventListener('DOMContentLoaded', () => {
            const inputs = document.querySelectorAll('.otp-digit');

            inputs.forEach((input, index) => {
                input.addEventListener('input', () => {
                    if (input.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && input.value === '' && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });

            // Attach paste only to the first input
            inputs[0].addEventListener('paste', (e) => {
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                if (/^\d{6}$/.test(paste)) {
                    e.preventDefault();
                    paste.split('').forEach((char, i) => {
                        if (inputs[i]) {
                            inputs[i].value = char;
                        }
                    });
                    inputs[5].focus();
                }
            });
        });




        document.addEventListener('DOMContentLoaded', function() {
            const resendButton = document.getElementById('resendOtp');
            const cooldownMessage = document.getElementById('otpCooldown');
            const timeLeftElement = document.getElementById('timeLeft');

            resendButton.addEventListener('click', function() {
                resendButton.disabled = true;

                fetch("{{ route('otp.resend') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        cooldownMessage.style.display = 'block';

                        if (data.status === 'error') {
                           
                            let secondsLeft = data.seconds_left;
                            cooldownMessage.classList.remove('text-success');
                            cooldownMessage.classList.add('text-danger');
                            cooldownMessage.innerHTML =
                                `Please wait <span id="timeLeft">${secondsLeft}</span> seconds before requesting again.`;

                            let interval = setInterval(() => {
                                secondsLeft--;
                                document.getElementById('timeLeft').textContent = secondsLeft;

                                if (secondsLeft <= 0) {
                                    clearInterval(interval);
                                    cooldownMessage.style.display = 'none';
                                    resendButton.disabled = false;
                                }
                            }, 1000);

                        } else {

                            cooldownMessage.classList.remove('text-danger');
                            cooldownMessage.classList.add('text-success');
                            cooldownMessage.innerHTML = '✅ OTP resent successfully!';

                            setTimeout(() => {
                                cooldownMessage.style.display = 'none';
                                resendButton.disabled = false;
                            }, 3000); 
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        resendButton.disabled = false;
                    });
            });
        });
    </script>
    <div class="container">




        <div class="modal fade" id="otpModal" tabindex="-1" role="dialog" aria-labelledby="otpModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="POST" action="{{ route('otp.verify') }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="otpModalLabel">🔐 Enter One-Time PIN</h5>
                        </div>
                        <div class="modal-body text-center">

                            <div class="d-flex justify-content-center gap-2 otp-input-group">
                                @for ($i = 0; $i < 6; $i++)
                                    <input type="text" name="otp[]" maxlength="1" pattern="[0-9]*" inputmode="numeric"
                                        class="form-control text-center otp-digit" required>
                                @endfor
                            </div>

                            {{-- @error('otp')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror --}}

                            {{-- @if ($errors->has('otp'))
                        <div class="text-danger mt-2">{{ $errors->first('otp') }}</div>
                    @endif --}}

                            <div id="otpCooldown" class="text-danger mt-2" style="display:none;">Please wait <span
                                    id="timeLeft"></span> seconds before requesting again.</div>


                            <a href="javascript:void(0);" id="resendOtp" class="btn btn-link">Resend OTP</a>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="submit" class="btn btn-primary w-100">Verify PIN</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
