<?php

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\User;
use App\Models\Supplier;
use App\Enums\SsmType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

new class extends Component
{
    public $step = 1;

    // Step 2 Verification Info
    public $ssm_no = '';
    public $duplicateError = '';
    public $verificationSuccess = false;

    // Steps 3-5 Supplier/User Info
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $company_name = '';
    public $ssm_type = 'ROC: SENDIRIAN BERHAD'; // Smart default matching SsmType enum backing value

    public function setStep(int $target): void
    {
        $this->step = $target;
    }

    public function verifySSM(): void
    {
        $this->validate([
            'ssm_no' => 'required|string|max:50',
        ]);

        $this->duplicateError = '';
        $this->verificationSuccess = false;

        $exists = Supplier::where('ssm_number', $this->ssm_no)
            ->orWhere('old_registration_number', $this->ssm_no)
            ->exists();

        if ($exists) {
            $this->duplicateError = 'This SSM registration number has already been onboarded.';
        } else {
            $this->verificationSuccess = true;
        }
    }

    public function proceedToStep3(): void
    {
        $this->step = 3;
    }

    public function proceedToStep4(): void
    {
        $this->validate([
            'name' => 'required|string|max:124',
            'email' => 'required|email|string|max:124|unique:users,email',
        ]);

        $this->step = 4;
    }

    public function proceedToStep5(): void
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'ssm_type' => ['required', Rule::enum(SsmType::class)],
        ]);

        $this->step = 5;
    }

    public function register(): mixed
    {
        $this->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create User
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Create Supplier
        Supplier::create([
            'user_id' => $user->id,
            'company_name' => $this->company_name,
            'ssm_type' => $this->ssm_type,
            'ssm_number' => $this->ssm_no,
            'is_submitted' => false,
            'application_status' => 'Pending',
        ]);

        // Log in
        auth()->login($user);

        // Redirect
        return redirect()->route('dashboard');
    }
};
?>

<div class="space-y-6">
    <!-- Step indicators -->
    <nav class="flex items-center justify-between px-1 mb-6" aria-label="Progress Tracker">
        <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold {{ $step >= 1 ? 'bg-emerald-600 text-white shadow-xs' : 'bg-zinc-150 text-zinc-400 dark:bg-zinc-800' }}">1</span>
            <span class="text-[10px] font-semibold {{ $step == 1 ? 'text-zinc-950 dark:text-white' : 'text-zinc-400 dark:text-zinc-550' }}">Check</span>
        </div>
        <div class="flex-1 h-px bg-zinc-200 dark:bg-zinc-800 mx-1.5" aria-hidden="true"></div>
        <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold {{ $step >= 2 ? 'bg-emerald-600 text-white shadow-xs' : 'bg-zinc-150 text-zinc-400 dark:bg-zinc-800' }}">2</span>
            <span class="text-[10px] font-semibold {{ $step == 2 ? 'text-zinc-950 dark:text-white' : 'text-zinc-400 dark:text-zinc-550' }}">Verify</span>
        </div>
        <div class="flex-1 h-px bg-zinc-200 dark:bg-zinc-800 mx-1.5" aria-hidden="true"></div>
        <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold {{ $step >= 3 ? 'bg-emerald-600 text-white shadow-xs' : 'bg-zinc-150 text-zinc-400 dark:bg-zinc-800' }}">3</span>
            <span class="text-[10px] font-semibold {{ $step == 3 ? 'text-zinc-950 dark:text-white' : 'text-zinc-400 dark:text-zinc-550' }}">About</span>
        </div>
        <div class="flex-1 h-px bg-zinc-200 dark:bg-zinc-800 mx-1.5" aria-hidden="true"></div>
        <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold {{ $step >= 4 ? 'bg-emerald-600 text-white shadow-xs' : 'bg-zinc-150 text-zinc-400 dark:bg-zinc-800' }}">4</span>
            <span class="text-[10px] font-semibold {{ $step == 4 ? 'text-zinc-950 dark:text-white' : 'text-zinc-400 dark:text-zinc-550' }}">Company</span>
        </div>
        <div class="flex-1 h-px bg-zinc-200 dark:bg-zinc-800 mx-1.5" aria-hidden="true"></div>
        <div class="flex items-center gap-1.5">
            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold {{ $step >= 5 ? 'bg-emerald-600 text-white shadow-xs' : 'bg-zinc-150 text-zinc-400 dark:bg-zinc-800' }}">5</span>
            <span class="text-[10px] font-semibold {{ $step == 5 ? 'text-zinc-950 dark:text-white' : 'text-zinc-400 dark:text-zinc-550' }}">Security</span>
        </div>
    </nav>

    <!-- Step 1: Account Status Check -->
    @if ($step === 1)
        <div class="space-y-6 text-center py-4">
            <div class="p-3 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full w-12 h-12 flex items-center justify-center mx-auto" aria-hidden="true">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div class="space-y-2">
                <h3 class="text-lg font-bold text-zinc-950 dark:text-white">Are you already registered with openPembekalan?</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto">This registration portal is exclusively for new Suppliers to onboarding into our procurement ecosystem.</p>
            </div>
            
            <div class="flex flex-col gap-3 pt-2">
                <button wire:click="setStep(2)" type="button" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 text-white hover:bg-emerald-500 rounded-xl text-xs font-bold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    No, I am a new Supplier
                </button>
                <a href="/login" class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-zinc-200 dark:border-zinc-800 bg-white/60 dark:bg-zinc-900/60 backdrop-blur-md text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-855 hover:text-zinc-950 dark:hover:text-white rounded-xl text-xs font-bold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                    Yes, take me to Sign In
                </a>
            </div>
        </div>
    @endif

    <!-- Step 2: SSM Verification -->
    @if ($step === 2)
        <div class="space-y-4">
            <div class="text-center mb-4">
                <h3 class="text-base font-bold text-zinc-950 dark:text-white">Verify SSM Registration</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Check if your company has already been registered on our system.</p>
            </div>

            <form wire:submit.prevent="verifySSM" class="space-y-4">
                <x-ui.input wire:model="ssm_no" id="ssm_no" type="text" label="SSM / Registration Number" placeholder="e.g. 1234567-A" required error="{{ $errors->first('ssm_no') }}">
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </x-slot:icon>
                </x-ui.input>

                <div class="flex items-center gap-3">
                    <button wire:click="setStep(1)" type="button" class="w-1/3 py-2.5 border border-zinc-200 dark:border-zinc-800 bg-white/60 dark:bg-zinc-900/60 text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white rounded-xl text-xs font-bold transition-all focus:outline-none">
                        Back
                    </button>
                    <x-ui.button type="submit" class="w-2/3">
                        Verify SSM Status
                    </x-ui.button>
                </div>
            </form>

            @if ($duplicateError)
                <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/40 text-rose-800 dark:text-rose-400 rounded-xl p-4 text-xs space-y-3 mt-4">
                    <p class="font-semibold">{{ $duplicateError }}</p>
                    <p class="leading-relaxed">To access this company profile, please sign in. If you think this is a mistake, contact administration.</p>
                    <a href="/login" class="inline-flex justify-center items-center px-3 py-1.5 bg-rose-600 text-white rounded-lg font-bold hover:bg-rose-500 transition-colors">
                        Go to Sign In
                    </a>
                </div>
            @endif

            @if ($verificationSuccess)
                <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-700/40 text-emerald-800 dark:text-emerald-400 rounded-xl p-4 text-xs space-y-3 mt-4">
                    <p class="font-semibold">Verification Cleared!</p>
                    <p>No existing profile was found for SSM number <span class="font-bold font-mono">{{ $ssm_no }}</span>. You are eligible to register.</p>
                    <button wire:click="proceedToStep3" type="button" class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-500 transition-colors">
                        Continue to About You &rarr;
                    </button>
                </div>
            @endif
        </div>
    @endif

    <!-- Step 3: Tell Us About Yourself -->
    @if ($step === 3)
        <div class="space-y-4">
            <div class="text-center mb-4">
                <h3 class="text-base font-bold text-zinc-950 dark:text-white">Tell us about yourself</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Please provide your personal contact info to manage this account.</p>
            </div>

            <form wire:submit.prevent="proceedToStep4" class="space-y-4">
                <x-ui.input wire:model="name" id="name" type="text" label="Full Name" placeholder="e.g. Ammar Yasir" required error="{{ $errors->first('name') }}">
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </x-slot:icon>
                </x-ui.input>

                <x-ui.input wire:model="email" id="email" type="email" label="Work Email Address" placeholder="e.g. name@company.com" required error="{{ $errors->first('email') }}">
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                        </svg>
                    </x-slot:icon>
                </x-ui.input>

                <div class="flex items-center gap-3">
                    <button wire:click="setStep(2)" type="button" class="w-1/3 py-2.5 border border-zinc-200 dark:border-zinc-800 bg-white/60 dark:bg-zinc-900/60 text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white rounded-xl text-xs font-bold transition-all focus:outline-none">
                        Back
                    </button>
                    <x-ui.button type="submit" class="w-2/3">
                        Continue to Company Info &rarr;
                    </x-ui.button>
                </div>
            </form>
        </div>
    @endif

    <!-- Step 4: Company Profile Info -->
    @if ($step === 4)
        <div class="space-y-4">
            <div class="text-center mb-4">
                <h3 class="text-base font-bold text-zinc-950 dark:text-white">Supplier Company Profile</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Provide your business operating details.</p>
            </div>

            <form wire:submit.prevent="proceedToStep5" class="space-y-4">
                <x-ui.input wire:model="company_name" id="company_name" type="text" label="Company Name" placeholder="e.g. Acme Logistics Sdn Bhd" required error="{{ $errors->first('company_name') }}">
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </x-slot:icon>
                </x-ui.input>

                <div class="space-y-1.5 w-full">
                    <x-ui.label for="ssm_type">SSM Type</x-ui.label>
                    <div class="relative rounded-xl shadow-xs">
                        <select wire:model="ssm_type" id="ssm_type" class="block w-full rounded-xl border border-zinc-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 py-2.5 px-3.5 text-sm transition-all focus:border-emerald-500 dark:focus:border-emerald-400 focus:ring-1 focus:ring-emerald-500 dark:focus:ring-emerald-400 focus:outline-none">
                            <option value="ROC: SENDIRIAN BERHAD">Sdn Bhd (Sendirian Berhad)</option>
                            <option value="ROC: BERHAD">Berhad</option>
                            <option value="ROB: PERSEORANGAN">Sole Proprietorship (Perseorangan)</option>
                            <option value="ROB: PERKONGSIAN">Partnership (Perkongsian)</option>
                            <option value="ROS: PERTUBUHAN">Pertubuhan</option>
                        </select>
                    </div>
                </div>

                <x-ui.input wire:model="ssm_no" id="verified_ssm_no" type="text" label="SSM Number (Verified)" disabled>
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </x-slot:icon>
                </x-ui.input>

                <div class="flex items-center gap-3">
                    <button wire:click="setStep(3)" type="button" class="w-1/3 py-2.5 border border-zinc-200 dark:border-zinc-800 bg-white/60 dark:bg-zinc-900/60 text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white rounded-xl text-xs font-bold transition-all focus:outline-none">
                        Back
                    </button>
                    <x-ui.button type="submit" class="w-2/3">
                        Continue to Password &rarr;
                    </x-ui.button>
                </div>
            </form>
        </div>
    @endif

    <!-- Step 5: Password Creation -->
    @if ($step === 5)
        <div class="space-y-4">
            <div class="text-center mb-4">
                <h3 class="text-base font-bold text-zinc-950 dark:text-white">Create Security Password</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Setup password details to complete registration.</p>
            </div>

            <form wire:submit.prevent="register" class="space-y-4">
                <x-ui.input wire:model="password" id="password" type="password" label="Account Password" placeholder="Minimum 8 characters" required error="{{ $errors->first('password') }}">
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </x-slot:icon>
                </x-ui.input>

                <x-ui.input wire:model="password_confirmation" id="password_confirmation" type="password" label="Confirm Password" placeholder="••••••••" required>
                    <x-slot:icon>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </x-slot:icon>
                </x-ui.input>

                <div class="flex items-center gap-3">
                    <button wire:click="setStep(4)" type="button" class="w-1/3 py-2.5 border border-zinc-200 dark:border-zinc-800 bg-white/60 dark:bg-zinc-900/60 text-zinc-700 dark:text-zinc-300 hover:text-zinc-950 dark:hover:text-white rounded-xl text-xs font-bold transition-all focus:outline-none">
                        Back
                    </button>
                    <x-ui.button type="submit" class="w-2/3">
                        Complete Onboarding
                    </x-ui.button>
                </div>
            </form>
        </div>
    @endif
</div>
