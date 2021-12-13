<template>
    <div>
        <div class="row p-4">
            <a href="/home" class="text-light"><i class="fas fa-lg fa-home"></i></a>
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
                    <tr>
                        <td class="text-light">Σ</td>
                        <td class="text-light">{{ totalDiffWorthIfHolding().toFixed(0) }}</td>
                        <td class="text-light">{{ perWeekDiffWorthIfHolding().toFixed(0) }}</td>
                        <td class="text-light">{{ perMonthDiffWorthIfHolding().toFixed(0) }}</td>
                        <td class="text-light">{{ totalDiffInput().toFixed(0) }}</td>
                        <td class="text-light">{{ perWeekDiffInput().toFixed(0) }}</td>
                        <td class="text-light">{{ perMonthDiffInput().toFixed(0) }}</td>
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
        totalDiffWorthIfHolding: function () {
            var tdwih = 0;
            this.active.forEach(function(item) {
                tdwih += (item.value - item.wbw);
            });
            return tdwih;
        },
        perWeekDiffWorthIfHolding: function () {
            var mdwih = 0;
            this.active.forEach(function(item) {
                mdwih += ((item.value - item.wbw) / (item.seconds / 604800));
            });
            return mdwih;
        },
        perMonthDiffWorthIfHolding: function () {
            var wdwih = 0;
            this.active.forEach(function(item) {
                wdwih += ((item.value - item.wbw) / (item.seconds / 2628000));
            });
            return wdwih;
        },
        totalDiffInput: function() {
            var tdi = 0;
            this.active.forEach(function(item) {
                tdi += (item.value - item.total_input);
            });
            return tdi;
        },
        perWeekDiffInput: function() {
            var wdi = 0;
            this.active.forEach(function(item) {
                wdi += ((item.value - item.total_input) / (item.seconds / 604800));
            });
            return wdi;
        },
        perMonthDiffInput: function() {
            var mdi = 0;
            this.active.forEach(function(item) {
                mdi += ((item.value - item.total_input) / (item.seconds / 2628000));
            });
            return mdi;
        }
    },
}

</script>
