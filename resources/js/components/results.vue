<template>
    <div>
        <div class="row p-4">
            <div class="col-12">
                <ul class="nav nav-tabs justify-content-end">
                    <li class="nav-item">
                        <a class="nav-link text-light" href="/home"><i class="fas fa-home"></i></a>
                    </li>
                </ul>
            </div>
            <table class="table table-borderless table-striped table-hover table-vcenter">
                <thead>
                    <tr>
                        <th class="text-light"><i class="fa fa-bolt"></i></th>
                        <th class="text-light" colspan="3">Δ WIH</th>
                        <th class="text-light" colspan="3">Δ Input</th>
                    </tr>
                    <tr>
                        <th class="text-light">Pair</th>
                        <th class="text-light">Σ</th>
                        <th class="text-light">/ W</th>
                        <th class="text-light">/ M</th>
                        <th class="text-light">Σ</th>
                        <th class="text-light">/ W</th>
                        <th class="text-light">/ M</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in active">
                        <td class="text-light">{{ item.pair }}</td>
                        <td class="text-light">{{ (item.value - item.wbw).toFixed(0) }}</td>
                        <td class="text-light">{{ ((item.value - item.wbw) / (item.seconds / 604800)).toFixed(0) }}</td>
                        <td class="text-light">{{ ((item.value - item.wbw) / (item.seconds / 2628000)).toFixed(0) }}</td>
                        <td class="text-light">{{ (item.value - item.total_input).toFixed(0) }}</td>
                        <td class="text-light">{{ ((item.value - item.total_input) / (item.seconds / 604800)).toFixed(0) }}</td>
                        <td class="text-light">{{ ((item.value - item.total_input) / (item.seconds / 2628000)).toFixed(0) }}</td>
                    </tr>
                    <tr class="bg-black">
                        <td class="text-light">Σ</td>
                        <td class="text-light">{{ totalDiffWorthIfHolding('active').toFixed(0) }}</td>
                        <td class="text-light">{{ perWeekDiffWorthIfHolding('active').toFixed(0) }}</td>
                        <td class="text-light">{{ perMonthDiffWorthIfHolding('active').toFixed(0) }}</td>
                        <td class="text-light">{{ totalDiffInput('active').toFixed(0) }}</td>
                        <td class="text-light">{{ perWeekDiffInput('active').toFixed(0) }}</td>
                        <td class="text-light">{{ perMonthDiffInput('active').toFixed(0) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row p-4">
            <table class="table table-borderless table-striped table-hover table-vcenter">
                <thead>
                <tr>
                    <th class="text-light"><i class="fa fa-book"></i></th>
                    <th class="text-light" colspan="3">Δ WIH</th>
                    <th class="text-light" colspan="3">Δ Input</th>
                </tr>
                <tr>
                    <th class="text-light">Pair</th>
                    <th class="text-light">Σ</th>
                    <th class="text-light">/ W</th>
                    <th class="text-light">/ M</th>
                    <th class="text-light">Σ</th>
                    <th class="text-light">/ W</th>
                    <th class="text-light">/ M</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in chived">
                    <td class="text-light">{{ item.pair }}</td>
                    <td class="text-light">{{ (item.value - item.wbw).toFixed(0) }}</td>
                    <td class="text-light">{{ ((item.value - item.wbw) / (item.seconds / 604800)).toFixed(0) }}</td>
                    <td class="text-light">{{ ((item.value - item.wbw) / (item.seconds / 2628000)).toFixed(0) }}</td>
                    <td class="text-light">{{ (item.value - item.total_input).toFixed(0) }}</td>
                    <td class="text-light">{{ ((item.value - item.total_input) / (item.seconds / 604800)).toFixed(0) }}</td>
                    <td class="text-light">{{ ((item.value - item.total_input) / (item.seconds / 2628000)).toFixed(0) }}</td>
                </tr>
                <tr class="bg-black">
                    <td class="text-light">Σ</td>
                    <td class="text-light">{{ totalDiffWorthIfHolding('chived').toFixed(0) }}</td>
                    <td class="text-light">{{ perWeekDiffWorthIfHolding('chived').toFixed(0) }}</td>
                    <td class="text-light">{{ perMonthDiffWorthIfHolding('chived').toFixed(0) }}</td>
                    <td class="text-light">{{ totalDiffInput('chived').toFixed(0) }}</td>
                    <td class="text-light">{{ perWeekDiffInput('chived').toFixed(0) }}</td>
                    <td class="text-light">{{ perMonthDiffInput('chived').toFixed(0) }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            active: [],
            chived: [],
        };
    },

    mounted() {
        this.getActive();
        this.getChived();
    },

    methods: {
        getActive: function() {
            axios.get("/results/data", {
                params: {
                    type: 'active',
                }
            }).then(response => {
                this.active = response.data;
            });
        },
        getChived: function() {
            axios.get("/results/data", {
                params: {
                    type: 'archived',
                }
            }).then(response => {
                this.chived = response.data;
            });
        },
        totalDiffWorthIfHolding: function (type) {
            var tdwih = 0;
            this[type].forEach(function(item) {
                tdwih += (item.value - item.wbw);
            });
            return tdwih;
        },
        perWeekDiffWorthIfHolding: function (type) {
            var mdwih = 0;
            this[type].forEach(function(item) {
                mdwih += ((item.value - item.wbw) / (item.seconds / 604800));
            });
            return mdwih;
        },
        perMonthDiffWorthIfHolding: function (type) {
            var wdwih = 0;
            this[type].forEach(function(item) {
                wdwih += ((item.value - item.wbw) / (item.seconds / 2628000));
            });
            return wdwih;
        },
        totalDiffInput: function(type) {
            var tdi = 0;
            this[type].forEach(function(item) {
                tdi += (item.value - item.total_input);
            });
            return tdi;
        },
        perWeekDiffInput: function(type) {
            var wdi = 0;
            this[type].forEach(function(item) {
                wdi += ((item.value - item.total_input) / (item.seconds / 604800));
            });
            return wdi;
        },
        perMonthDiffInput: function(type) {
            var mdi = 0;
            this[type].forEach(function(item) {
                mdi += ((item.value - item.total_input) / (item.seconds / 2628000));
            });
            return mdi;
        }
    },
}

</script>
<style>
.bg-black {
    background-color: black !important;
}
</style>
