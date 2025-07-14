<x-filament-panels::page>
    <div class="space-y-6">
        <div class="text-center space-y-6">
            <h1 class="text-3xl font-bold">في نظام إدارة المستشفى</h1>
            <p class="text-gray-500 text-lg">اختر القسم الذي تود الدخول إليه</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">

                @if (auth()->user()->can('add_appointments') ||
                        auth()->user()->can('view_appointments') ||
                        auth()->user()->can('manage_appointments'))
                    <x-filament::button size="xl" class="text-xl py-6 w-full flex items-center justify-center"
                        tag="a" href="{{ route('filament.admin.resources.appointments.index') }}">
                        {{ __('keywords.appointments') }}
                    </x-filament::button>
                @endif

                @if (auth()->user()->can('manage_patients') || auth()->user()->can('view_patients'))
                    <x-filament::button size="xl" class="text-xl py-6 w-full flex items-center justify-center"
                        tag="a" href="{{ route('filament.admin.resources.patients.index') }}">
                        {{ __('keywords.patients') }}
                    </x-filament::button>
                @endif

                @if (auth()->user()->can('manage_medicines'))
                    <x-filament::button size="xl" class="text-xl py-6 w-full flex items-center justify-center"
                        tag="a" href="{{ route('filament.admin.resources.medicines.index') }}">
                        {{ __('keywords.medicines') }}
                    </x-filament::button>
                @endif

                @if (auth()->user()->can('manage_medical_tests'))
                    <x-filament::button size="xl" class="text-xl py-6 w-full flex items-center justify-center"
                        tag="a" href="{{ route('filament.admin.resources.medical-tests.index') }}">
                        {{ __('keywords.medical_tests') }}
                    </x-filament::button>
                @endif

                @if (auth()->user()->can('manage_radiology_tests'))
                    <x-filament::button size="xl" class="text-xl py-6 w-full flex items-center justify-center"
                        tag="a" href="{{ route('filament.admin.resources.radiology-tests.index') }}">
                        {{ __('keywords.radiology_tests') }}
                    </x-filament::button>
                @endif


                @if (auth()->user()->can('manage_food'))
                    <x-filament::button size="xl" class="text-xl py-6 w-full flex items-center justify-center"
                        tag="a" href="{{ route('filament.admin.resources.food.index') }}">
                        {{ __('keywords.food') }}
                    </x-filament::button>
                @endif

                @if (auth()->user()->can('view_reports'))
                    <x-filament::button size="xl" class="text-xl py-6 w-full flex items-center justify-center"
                        tag="a" href="{{ route('filament.admin.resources.appointment-submissions.index') }}">
                        {{ __('keywords.appointment_submissions') }}
                    </x-filament::button>
                @endif

                @if (auth()->user()->can('manage_visit_types'))
                    <x-filament::button size="xl" class="text-xl py-6 w-full flex items-center justify-center"
                        tag="a" href="{{ route('filament.admin.resources.visit-types.index') }}">
                        {{ __('keywords.visit_types') }}
                    </x-filament::button>
                @endif

                @if (auth()->user()->can('manage_clinics'))
                    <x-filament::button size="xl" class="text-xl py-6 w-full flex items-center justify-center"
                        tag="a" href="{{ route('filament.admin.resources.clinics.index') }}">
                        {{ __('keywords.clinics') }}
                    </x-filament::button>
                @endif

                @if (auth()->user()->can('manage_specialties'))
                    <x-filament::button size="xl" class="text-xl py-6 w-full flex items-center justify-center"
                        tag="a" href="{{ route('filament.admin.resources.specialties.index') }}">
                        {{ __('keywords.specialties') }}
                    </x-filament::button>
                @endif

                @if (auth()->user()->can('manage_roles'))
                    <x-filament::button size="xl" class="text-xl py-6 w-full flex items-center justify-center"
                        tag="a" href="{{ route('filament.admin.resources.roles.index') }}">
                        {{ __('keywords.roles') }}
                    </x-filament::button>
                @endif

                @if (auth()->user()->can('manage_users'))
                    <x-filament::button size="xl" class="text-xl py-6 w-full flex items-center justify-center"
                        tag="a" href="{{ route('filament.admin.resources.users.index') }}">
                        {{ __('keywords.users') }}
                    </x-filament::button>
                @endif

            </div>

            <!-- LIVE TIME -->
            <div x-data="{
                time: '',
                date: '',
                start() {
                    this.update();
                    setInterval(() => this.update(), 1000);
                },
                update() {
                    const now = new Date();
                    let hours = now.getHours();
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    const seconds = String(now.getSeconds()).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const year = now.getFullYear();
                    const ampm = hours >= 12 ? 'PM' : 'AM';
                    hours = hours % 12;
                    hours = hours ? hours : 12; // handle 0 => 12
                    hours = String(hours).padStart(2, '0');
                    this.time = `${hours}:${minutes}:${seconds} ${ampm}`;
                    this.date = `${day}/${month}/${year}`;
                }
            }" x-init="start()" class="mt-8 text-xl text-gray-400 space-y-1">
                <div>الوقت الحالي:</div>
                <div x-text="time"></div>
                <div x-text="date"></div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
