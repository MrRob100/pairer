<template>
    <div>
        <div class="row p-4">
            <div class="col-12">
                <ul class="nav nav-tabs justify-content-end">
                    <li class="nav-item">
                        <a class="nav-link text-light" href="/"><i class="fas fa-home"></i></a>
                    </li>
                </ul>
            </div>

            <table class="table table-borderless table-striped table-hover table-vcenter" id="active_pairs">
                <thead>
                    <tr>
                        <th class="text-light"><i class="fa fa-bolt"></i></th>
                        <th class="text-light" colspan="3">Δ WIH</th>
                        <th class="text-light" colspan="3">Δ Input (profit)</th>
                    </tr>
                    <tr>
                        <th class="text-light" style="width:200px">Pair</th>
                        <th class="text-light"><button class="btn text-light" @click="metric.active = 'diff_if_holding'">Σ</button></th>
                        <th class="text-light">/ W</th>
                        <th class="text-light">/ M</th>
                        <th class="text-light"><button class="btn text-light" @click="metric.active = 'profit'">Σ</button></th>
                        <th></th>
                        <th></th>
                        <th class="text-light">/ W</th>
                        <th class="text-light">/ M</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in activeSorted">
                        <td class="text-light">{{ item.pair }}</td>
                        <td :class="colorClass(item.diff_if_holding)">{{ item.diff_if_holding.toFixed(0) }}</td>
                        <td class="text-light">{{ (item.diff_if_holding / (item.seconds / 604800)).toFixed(0) }}</td>
                        <td class="text-light">{{ (item.diff_if_holding / (item.seconds / 2628000)).toFixed(0) }}</td>
                        <td class="cell-narrow" :class="colorClass(item.profit)">{{ (item.profit).toFixed(0) }}</td>
                        <td class="cell-narrow" :class="colorClass(item.profit)">{{ ((item.profit / item.total_input) * 100).toFixed(1) }}%</td>
                        <td class="text-light">{{ (item.seconds / 2628000).toFixed(1) }} months</td>
                        <td class="text-light">{{ ((item.profit) / (item.seconds / 604800)).toFixed(0) }}</td>
                        <td class="text-light">{{ ((item.profit) / (item.seconds / 2628000)).toFixed(0) }}</td>
                    </tr>
                    <tr class="bg-black">
                        <td class="text-light">Σ</td>
                        <td :class="colorClass(totalDiffWorthIfHolding('active'))">{{ totalDiffWorthIfHolding('active').toFixed(0) }}</td>
                        <td class="text-light">{{ perWeekDiffWorthIfHolding('active').toFixed(0) }}</td>
                        <td class="text-light">{{ perMonthDiffWorthIfHolding('active').toFixed(0) }}</td>
                        <td :class="colorClass(totalDiffInput('active'))">{{ totalDiffInput('active').toFixed(0) }}</td>
                        <td :class="colorClass(totalDiffInput('active'))">{{ profitPercent('active') }}</td>
                        <td class="text-light">{{ (active_time / 2628000).toFixed(1) }} months</td>
                        <td class="text-light">{{ perWeekDiffInput('active').toFixed(0) }}</td>
                        <td class="text-light">{{ perMonthDiffInput('active').toFixed(0) }}</td>
                    </tr>
                    <tr>
                    <tr v-if="loading_active">
                        <td colspan="9" class="text-center">
                            <div class="spinner-border"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row p-4">
            <table class="table table-borderless table-striped table-hover table-vcenter" id="chived_pairs">
                <thead>
                <tr>
                    <th class="text-light"><i class="fa fa-book"></i></th>
                    <th class="text-light" colspan="3">Δ WIH</th>
                    <th class="text-light" colspan="3">Δ Input (profit)</th>
                </tr>
                <tr>
                    <th class="text-light" style="width:200px">Pair</th>
                    <th class="text-light"><button class="btn text-light" @click="metric.chived = 'diff_if_holding'">Σ</button></th>
                    <th class="text-light">/ W</th>
                    <th class="text-light">/ M</th>
                    <th class="text-light"><button class="btn text-light" @click="metric.chived = 'profit'">Σ</button></th>
                    <th></th>
                    <th></th>
                    <th class="text-light">/ W</th>
                    <th class="text-light">/ M</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in chivedSorted">
                    <td class="text-light">{{ item.pair }}</td>
                    <td :class="colorClass(item.diff_if_holding)">{{ item.diff_if_holding.toFixed(0) }}</td>
                    <td class="text-light">{{ (item.diff_if_holding / (item.seconds / 604800)).toFixed(0) }}</td>
                    <td class="text-light">{{ (item.diff_if_holding / (item.seconds / 2628000)).toFixed(0) }}</td>
                    <td class="cell-narrow" :class="colorClass(item.profit)">{{ (item.profit).toFixed(0) }}</td>
                    <td class="cell-narrow" :class="colorClass(item.profit)">{{ ((item.profit / item.total_input) * 100).toFixed(1) }}%</td>
                    <td class="text-light">{{ (item.seconds / 2628000).toFixed(1) }} months</td>
                    <td class="text-light">{{ ((item.profit) / (item.seconds / 604800)).toFixed(0) }}</td>
                    <td class="text-light">{{ ((item.profit) / (item.seconds / 2628000)).toFixed(0) }}</td>
                </tr>
                <tr class="bg-black">
                    <td class="text-light">Σ</td>
                    <td :class="colorClass(totalDiffWorthIfHolding('chived'))">{{ totalDiffWorthIfHolding('chived').toFixed(0) }}</td>
                    <td class="text-light">{{ perWeekDiffWorthIfHolding('chived').toFixed(0) }}</td>
                    <td class="text-light">{{ perMonthDiffWorthIfHolding('chived').toFixed(0) }}</td>
                    <td :class="colorClass(totalDiffInput('chived'))">{{ totalDiffInput('chived').toFixed(0) }}</td>
                    <td :class="colorClass(totalDiffInput('chived'))">{{ profitPercent('chived') }}</td>
                    <td class="text-light">{{ (chived_time / 2628000).toFixed(1) }} months</td>
                    <td class="text-light">{{ perWeekDiffInput('chived').toFixed(0) }}</td>
                    <td class="text-light">{{ perMonthDiffInput('chived').toFixed(0) }}</td>
                </tr>
                <tr v-if="loading_chived">
                    <td colspan="9" class="text-center">
                        <div class="spinner-border"></div>
                    </td>
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
            active_time: null,
            chived: [],
            chived_time: null,
            loading_active: true,
            loading_chived: true,
            metric: {
                active: 'diff_if_holding',
                chived: 'diff_if_holding',
            }
        };
    },

    mounted() {
        this.getActive();
        this.getChived();
    },

    computed: {
        activeSorted: function() {
            let _this = this;
            function compare(a, b) {
                if (a[_this.metric.active] > b[_this.metric.active]){
                    return -1;
                }
                if (a[_this.metric.active] < b[_this.metric.active]){
                    return 1;
                }
                return 0;
            }

            return this.active.sort(compare);
        },
        chivedSorted: function() {
            let _this = this;
            function compare(a, b) {
                if (a[_this.metric.chived] > b[_this.metric.chived]){
                    return -1;
                }
                if (a[_this.metric.chived] < b[_this.metric.chived]){
                    return 1;
                }
                return 0;
            }

            return this.chived.sort(compare);
        }
    },

    methods: {
        colorClass: function(number) {
            return number > 0 ? 'text-success' : 'text-danger';
        },
        getActive: function() {
            this.loading_active = true;
            axios.get("/results/data", {
                params: {
                    type: 'active',
                }
            }).then(response => {
                this.active = response.data.data;
                this.active_time = response.data.total_time;
                this.loading_active = false
            });
        },
        getChived: function() {
            this.loading_chived = true;
            axios.get("/results/data", {
                params: {
                    type: 'archived',
                }
            }).then(response => {
                this.chived = response.data.data;
                this.chived_time = response.data.total_time;
                this.loading_chived = false;
            });
        },
        totalDiffWorthIfHolding: function (type) {
            let tdwih = 0;
            this[type].forEach(function(item) {
                tdwih += (item.value - item.wbw);
            });
            return tdwih;
        },
        perWeekDiffWorthIfHolding: function (type) {
            let mdwih = 0;
            this[type].forEach(function(item) {
                mdwih += ((item.value - item.wbw) / (item.seconds / 604800));
            });
            return mdwih;
        },
        perMonthDiffWorthIfHolding: function (type) {
            let wdwih = 0;
            this[type].forEach(function(item) {
                wdwih += ((item.value - item.wbw) / (item.seconds / 2628000));
            });
            return wdwih;
        },
        profitPercent: function(type) {
            let input = 0;
            this[type].forEach(function(item) {
                input += item.total_input;
            });

            return ((this.totalDiffInput(type) / input) * 100).toFixed(1) + "%";
        },
        totalDiffInput: function(type) {
            let tdi = 0;
            this[type].forEach(function(item) {
                tdi += (item.value - item.total_input);
            });
            return tdi;
        },
        perWeekDiffInput: function(type) {
            let wdi = 0;
            this[type].forEach(function(item) {
                wdi += ((item.value - item.total_input) / (item.seconds / 604800));
            });
            return wdi;
        },
        perMonthDiffInput: function(type) {
            let mdi = 0;
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
.cell-narrow {
    width: 50px;
}
</style>
