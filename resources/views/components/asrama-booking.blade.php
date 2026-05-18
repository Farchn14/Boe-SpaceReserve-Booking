@props([
    'facilities' => [],
    'selectedId' => null
])

<div x-data="{
    // Local state
    duration: 1,
    adults: 1,
    children: 0,
    childAges: [],
    rooms: 1,
    selectedDate: null,
    currentMonth: new Date().getMonth(),
    currentYear: new Date().getFullYear(),
    daysInMonth: [],
    
    formatDateLocal(date) {
        if (!date) return '';
        const d = new Date(date);
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    },
    
    init() {
        this.updateDaysInMonth();
        this.$watch('children', val => {
            const count = parseInt(val) || 0;
            if (count > this.childAges.length) {
                for (let i = this.childAges.length; i < count; i++) {
                    this.childAges.push('');
                }
            } else {
                this.childAges = this.childAges.slice(0, count);
            }
        });
        this.$watch('adults', val => {
            if (this.rooms > val) {
                this.rooms = val;
            }
        });
        
        // Push initial values to parent
        this.$watch('packageType', val => this.$parent.packageType = val);
        this.$watch('selectedDate', val => {
            if (val) this.$parent.startDate = this.formatDateLocal(val);
        });
    },

    increment(field, max = null) {
        if (field === 'rooms' && this.rooms >= this.adults) {
            Swal.fire({
                title: 'Peringatan',
                text: 'Maximal 1 orang 1 kamar, mohon tambah jumlah orang/dewasa',
                icon: 'warning',
                confirmButtonColor: '#276AD7'
            });
            return;
        }
        if (max !== null && this[field] >= max) return;
        this[field]++;
        this.updateParentPrice();
    },

    decrement(field, min = 0) {
        if (this[field] > min) {
            this[field]--;
            this.updateParentPrice();
        }
    },

    updateParentPrice() {
        // Simple price calculation for asrama: duration * adults * facility_price
        // This is a placeholder, adjust based on actual business logic if needed
        const facility = this.$parent.currentFacility;
        if (!facility) return;
        
        let total = 0;
        if (this.packageType === 'bulanan') {
            total = this.duration * (facility.harga_bulanan || 0); 
        } else {
            total = this.duration * facility.harga;
        }
        
        // We override the parent's totalPrice by setting a dummy selected package or similar
        // Or simply let the parent's totalPrice logic handle it by providing the dates
        if (this.selectedDate) {
            const start = new Date(this.selectedDate);
            const end = new Date(start);
            if (this.packageType === 'harian') {
                end.setDate(start.getDate() + (parseInt(this.duration) - 1));
            } else {
                end.setMonth(start.getMonth() + parseInt(this.duration));
                end.setDate(end.getDate() - 1);
            }
            this.$parent.startDate = this.formatDateLocal(start);
            this.$parent.endDate = this.formatDateLocal(end);
        }
    },

    updateDaysInMonth() {
        const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
        const startDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
        
        this.daysInMonth = [];
        for (let i = 0; i < startDay; i++) {
            this.daysInMonth.push({ day: null, date: null });
        }
        for (let i = 1; i <= lastDay; i++) {
            const date = new Date(this.currentYear, this.currentMonth, i);
            this.daysInMonth.push({ day: i, date: date });
        }
    },

    nextMonth() {
        if (this.currentMonth === 11) {
            this.currentMonth = 0;
            this.currentYear++;
        } else {
            this.currentMonth++;
        }
        this.updateDaysInMonth();
    },

    prevMonth() {
        if (this.currentMonth === 0) {
            this.currentMonth = 11;
            this.currentYear--;
        } else {
            this.currentMonth--;
        }
        this.updateDaysInMonth();
    },

    selectDate(date) {
        if (!date) return;
        // Don't allow past dates
        const today = new Date();
        today.setHours(0,0,0,0);
        if (date < today) return;
        
        this.selectedDate = date;
        this.updateParentPrice();
    },

    isInRange(date) {
        if (!this.selectedDate || !date) return false;
        const start = new Date(this.selectedDate);
        start.setHours(0,0,0,0);
        const end = new Date(start);
        if (this.packageType === 'harian') {
            end.setDate(start.getDate() + (parseInt(this.duration) - 1));
        } else {
            end.setMonth(start.getMonth() + parseInt(this.duration));
            end.setDate(end.getDate() - 1);
        }
        date.setHours(0,0,0,0);
        return date >= start && date <= end;
    },

    get monthName() {
        return new Intl.DateTimeFormat('id-ID', { month: 'long' }).format(new Date(this.currentYear, this.currentMonth));
    },

    get canShowCalendar() {
        return this.duration > 0 && this.adults > 0;
    },

    get packageType() {
        return this.$parent.packageType;
    }
}" x-init="init()" class="w-full space-y-8">

    {{-- Package Selection --}}
    <div class="flex justify-center mb-8">
        <div class="inline-flex p-1 bg-gray-100 rounded-full shadow-inner">
            <button type="button" @click="$parent.packageType = 'harian'; selectedDate = null" 
                :class="$parent.packageType === 'harian' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                class="px-8 py-2.5 rounded-full font-bold text-sm transition-all duration-300 uppercase tracking-widest">
                Booking Harian
            </button>
            <button type="button" @click="$parent.packageType = 'bulanan'; selectedDate = null" 
                :class="$parent.packageType === 'bulanan' ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700'"
                class="px-8 py-2.5 rounded-full font-bold text-sm transition-all duration-300 uppercase tracking-widest">
                Booking Bulanan
            </button>
        </div>
    </div>

    {{-- Steppers Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Duration --}}
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
                Durasi (<span x-text="packageType === 'harian' ? 'Hari' : 'Bulan'"></span>)
            </label>
            <div class="flex items-center justify-between">
                <button type="button" @click="decrement('duration', 1)" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 font-black text-xl">-</button>
                <div class="text-3xl font-black text-gray-800" x-text="duration"></div>
                <button type="button" @click="increment('duration')" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 font-black text-xl">+</button>
            </div>
            <input type="hidden" name="duration" :value="duration">
        </div>

        {{-- Adults --}}
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Orang Dewasa</label>
            <div class="flex items-center justify-between">
                <button type="button" @click="decrement('adults', 1)" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 font-black text-xl">-</button>
                <div class="text-3xl font-black text-gray-800" x-text="adults"></div>
                <button type="button" @click="increment('adults')" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 font-black text-xl">+</button>
            </div>
            <input type="hidden" name="adults" :value="adults">
        </div>

        {{-- Children --}}
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Anak-anak</label>
            <div class="flex items-center justify-between">
                <button type="button" @click="decrement('children', 0)" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 font-black text-xl">-</button>
                <div class="text-3xl font-black text-gray-800" x-text="children"></div>
                <button type="button" @click="increment('children', 6)" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 font-black text-xl">+</button>
            </div>
            <input type="hidden" name="children_count" :value="children">
        </div>

        {{-- Rooms --}}
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-md">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Jumlah Kamar</label>
            <div class="flex items-center justify-between">
                <button type="button" @click="decrement('rooms', 1)" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 font-black text-xl">-</button>
                <div class="text-3xl font-black text-gray-800" x-text="rooms"></div>
                <button type="button" @click="increment('rooms')" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gray-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 font-black text-xl">+</button>
            </div>
            <input type="hidden" name="rooms_count" :value="rooms">
        </div>
    </div>

    {{-- Dynamic Child Age Inputs --}}
    <div x-show="children > 0" x-transition class="space-y-4 bg-blue-50/30 p-6 rounded-[2rem] border border-blue-100/50">
        <label class="block text-[10px] font-black text-blue-600 uppercase tracking-widest text-center mb-2">Umur Anak</label>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <template x-for="(age, index) in childAges" :key="index">
                <div class="relative group">
                    <input type="number" x-model="childAges[index]" :name="'child_age[]'" placeholder="Thn"
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-sm font-bold" min="0" max="17">
                    <span class="absolute -top-2 -left-2 bg-blue-600 text-white text-[8px] font-black w-5 h-5 flex items-center justify-center rounded-full" x-text="index + 1"></span>
                </div>
            </template>
        </div>
    </div>

    {{-- Interactive Calendar --}}
    <div x-show="canShowCalendar" x-transition class="pt-6">
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-xl overflow-hidden">
            {{-- Calendar Header --}}
            <div class="bg-gray-900 px-8 py-6 text-white flex items-center justify-between">
                <div>
                    <h3 class="text-2xl font-black tracking-tight" x-text="monthName"></h3>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest" x-text="currentYear"></p>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="prevMonth()" class="p-2 hover:bg-white/10 rounded-xl transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button type="button" @click="nextMonth()" class="p-2 hover:bg-white/10 rounded-xl transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>
            </div>

            {{-- Days Menu --}}
            <div class="grid grid-cols-7 gap-px bg-gray-100 border-b border-gray-100">
                <template x-for="day in ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']">
                    <div class="bg-gray-50 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest" x-text="day"></div>
                </template>
            </div>

            {{-- Calendar Grid --}}
            <div class="grid grid-cols-7 gap-px bg-gray-100">
                <template x-for="(item, index) in daysInMonth" :key="index">
                    <div class="h-16 sm:h-24 bg-white relative group cursor-pointer transition-all hover:bg-blue-50/50" 
                        @click="selectDate(item.date)"
                        :class="item.date && (item.date < new Date().setHours(0,0,0,0)) ? 'opacity-30 cursor-not-allowed bg-gray-50' : ''">
                        
                        <div x-show="item.day" class="absolute top-3 left-3 text-sm font-bold text-gray-400 group-hover:text-blue-600" 
                            :class="selectedDate && item.date && item.date.getTime() === selectedDate.getTime() ? 'text-blue-600 font-black' : ''"
                            x-text="item.day"></div>

                        {{-- Black Dot Logic --}}
                        <div x-show="item.day && isInRange(item.date)" 
                            x-transition
                            class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="w-3 h-3 bg-gray-900 rounded-full shadow-lg shadow-black/20 transform scale-100 animate-pulse"></div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Calendar Footer Info --}}
            <div class="px-8 py-4 bg-gray-50 flex flex-col sm:flex-row items-center justify-between border-t border-gray-100 gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-gray-900 rounded-full"></div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Rentang Terpilih</span>
                </div>
                <div x-show="selectedDate">
                    <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest" x-text="'Mulai: ' + new Intl.DateTimeFormat('id-ID', { dateStyle: 'long' }).format(selectedDate)"></span>
                </div>
            </div>
        </div>
        <input type="hidden" name="tgl_mulai" :value="formatDateLocal(selectedDate)">
    </div>
</div>
