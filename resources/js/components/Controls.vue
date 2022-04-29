<template>
    <div>
        <div v-if="pure">
            <div class="d-flex justify-content-center">
                <label class="text-light mx-1">{{ stopLimitSellLabel }}</label>
                <label class="text-light mx-1">{{ amount1to2 }}%</label>
            </div>
            <div class="d-flex justify-content-center">
                <button @click="slsPlus" class="ml-2 btn btn-success mb-2 d-inline align-top"><i class="fas fa-plus"></i></button>
                <button @click="slsMinus" class="ml-2 mr-2 btn btn-success mb-2 d-inline align-top"><i class="fas fa-minus"></i></button>
                <input class="form-control w-50 align-top d-inline mr-2" v-model="stopLimitSellPriceFloating" type="number" :step="step" :placeholder="stopLimitSellLabel">
                <button @click="setStopLimitSell(stopLimitSellPriceFloating)" class="btn btn-success mb-2 d-inline align-top"><i class="fas fa-check"></i></button>
                <button @click="cancelOrders('SELL')" class="ml-2 btn btn-success mb-2 d-inline align-top"><i class="fas fa-trash"></i></button>
            </div>
            <div class="d-flex justify-content-center">
                <input v-model=amount1to2 type="range" min="0" max="100" value="50" class="slider w-50 ml-25">
            </div>
            <br>
            <br>
            <div class="d-flex justify-content-center">
                <label class="text-light mx-1">{{ limitBuyLabel }}</label>
                <label class="text-light mx-1">{{ amount2to1 }}%</label>
            </div>
            <div class="d-flex justify-content-center">
                <button @click="lbPlus" class="ml-2 btn btn-success mb-2 d-inline align-top"><i class="fas fa-plus"></i></button>
                <button @click="lbMinus" class="ml-2 mr-2 btn btn-success mb-2 d-inline align-top"><i class="fas fa-minus"></i></button>
                <input class="form-control w-50 align-top d-inline mr-2" v-model="limitBuyPriceFloating" type="number" :step="step" :placeholder="limitBuyLabel">
                <button @click="setLimitBuy(limitBuyPriceFloating)" class="btn btn-success mb-2 d-inline align-top"><i class="fas fa-check"></i></button>
                <button @click="cancelOrders('BUY')" class="ml-2 btn btn-success mb-2 d-inline align-top"><i class="fas fa-trash"></i></button>
            </div>
            <div class="d-flex justify-content-center">
                <input v-model=amount2to1 type="range" min="0" max="100" value="50" class="slider w-50 ml-25">
            </div>
        </div>
        <div v-else>
            <button @click="transfer(symbol1, symbol2, (1/splitLow) * 100)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol2 }} 25%</button>
            <button @click="transfer(symbol1, symbol2, (1/split) * 100)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol2 }} {{ split }}%</button>
            <button @click="transfer(symbol1, symbol2, 1)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol2 }} 100%</button>
            <button @click="transfer(symbol2, symbol1, (1/splitLow) * 100)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol1 }} 25%</button>
            <button @click="transfer(symbol2, symbol1, (1/split) * 100)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol1 }} {{ split }}%</button>
            <button @click="transfer(symbol2, symbol1, 1)" class="btn btn-info mb-2" :disabled="disabled">Buy {{ symbol1 }} 100%</button>
        </div>
        <br>
        <hr>
        <button @click="getBalance(symbol1, 'one')" class="btn btn-info mb-2">Balance {{ symbol1 }}: {{ bal.one }} (${{ Math.floor(bal.oneUSD) }})</button>
<!--        <button @click="shave.one.show ? shave.one.show = false : shave.one.show = true" class="btn btn-info mb-2"><i class="fas fa-cut"></i></button>-->
<!--        <span v-if="shave.one.show">-->
<!--            <input class="form-control w-25 align-top d-inline" type="number" step="any" :placeholder="'amount ' + symbol1">-->
<!--            <button @click="shaveBal(symbol1, shave.one.amountUSD)" class="btn btn-success mb-2 d-inline"><i class="fas fa-check"></i></button>-->
<!--        </span>-->
<!--        <button @click="pump.one.show ? pump.one.show = false : pump.one.show = true" class="btn btn-info mb-2"><i class="fas fa-pump-soap"></i></button>-->
<!--        <span v-if="pump.one.show">-->
<!--            <input class="form-control w-25 align-top d-inline" type="number" step="any" :placeholder="'amount ' + symbol1">-->
<!--            <button @click="shaveBal(symbol1, pump.one.amountUSD)" class="btn btn-success mb-2 d-inline"><i class="fas fa-check"></i></button>-->
<!--        </span>-->
        <br>
        <button @click="getBalance(symbol2, 'two')" class="btn btn-info mb-2">Balance {{ symbol2 }}: {{ bal.two }} (${{ Math.floor(bal.twoUSD) }})</button>
<!--        <button @click="shave.two.show ? shave.two.show = false : shave.two.show = true" class="btn btn-info mb-2"><i class="fas fa-cut"></i></button>-->
<!--        <span v-if="shave.two.show">-->
<!--            <input class="form-control w-25 align-top d-inline" type="number" step="any" :placeholder="'amount ' + symbol1">-->
<!--            <button @click="pumpBal(symbol2, shave.two.amountUSD)" class="btn btn-success mb-2 d-inline"><i class="fas fa-check"></i></button>-->
<!--        </span>-->
<!--        <button @click="pump.two.show ? pump.two.show = false : pump.two.show = true" class="btn btn-info mb-2"><i class="fas fa-pump-soap"></i></button>-->
<!--        <span v-if="pump.two.show">-->
<!--            <input class="form-control w-25 align-top d-inline" type="number" step="any" :placeholder="'amount ' + symbol1">-->
<!--            <button @click="pumpBal(symbol1, pump.two.amountUSD)" class="btn btn-success mb-2 d-inline"><i class="fas fa-check"></i></button>-->
<!--        </span>-->
        <br>
        <button @click="getBalance('USDT', 'usdt')" class="btn btn-info mb-2">Balance USDT: ${{ Math.floor(bal.usdt) }}</button>
        <br>
        <button @click="showInputForm()" class="btn btn-info mb-2">Add Input</button>
        <div v-if="showForm" class="mt-3">
            <form v-on:submit.prevent>
                <div class="form-group col-3 pl-0">
                    <input class="form-control mb-2" v-model="input.one" type="number" step="any" :placeholder="'amount ' + symbol1">
                    <input class="form-control mb-2" v-model="input.oneUSD" type="number" step="any" :placeholder="'amount ' + symbol1 + ' usd'">
                    <input class="form-control mb-2" v-model="input.two" type="number" step="any" :placeholder="'amount ' + symbol2">
                    <input class="form-control mb-2" v-model="input.twoUSD" type="number" step="any" :placeholder="'amount ' + symbol2 + ' usd'">
                    <button @click="createInputRecord()" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        "tr",
        "pr",
        "br",
        "cr",
        "symbol1",
        "symbol2",
        "rr",
        "shaver",
        "pumpr",
        "pure",
        "pushLasts"
    ],
    data: function() {
        return {
            amount1to2: 0,
            amount2to1: 0,
            shave: {
                one: {
                    show: false,
                    amountUSD: null,
                },
                two: {
                    show: false,
                    amountUSD: null,
                },
            },
            pump: {
                one: {
                    show: false,
                    amountUSD: null,
                },
                two: {
                    show: false,
                    amountUSD: null,
                },
            },
            bal: {
                one: null,
                oneUSD: null,
                two: null,
                twoUSD: null,
                usdt: null
            },
            disabled: false,
            limitBuyPrice: null,
            limitBuyPriceFloating: null,
            lotSize: null,
            stopLimitSellPrice: null,
            stopLimitSellPriceFloating: null,
            showForm: false,
            input: {
                symbol1: null,
                one: null,
                oneUSD: null,
                symbol2: null,
                two: null,
                twoUSD: null,
            },
            splitLow: 25,
            split: 44.5,
            step: null,
        };
    },

    methods: {
        shaveBal: function(symbol, amountUSD) {
            axios.post(this.shaver, {
                params: {
                    symbol: symbol,
                    amountUSD: amountUSD,
                }
            }).then(function(response) {
                this.shave.one.show = false;
                this.shave.two.show = false;
            });
        },
        pumpBal: function(symbol, amountUSD) {
            axios.post(this.pumpr, {
                params: {
                    symbol: symbol,
                    amountUSD: amountUSD,
                }
            }).then(function(response) {
                this.pump.one.show = false;
                this.pump.two.show = false;
            });
        },
        createInputRecord: function() {
            this.input.symbol1 = this.symbol1;
            this.input.symbol2 = this.symbol2;

            axios.post(this.rr, this.input).then(() => {
                this.showForm = false;
            }).catch(error => {
                console.error(error);
            })
        },
        showInputForm: function() {
            this.showForm = !this.showForm;
        },
        getBalance: function(symbol, which) {
            let _this = this;
            axios.get(this.br, {
                params: {
                    of: symbol,
                }
            }).then(function (response) {
                _this.bal[which] = response.data;
                if (which !== 'usdt') {
                    _this.inUSD(symbol, response.data, which);
                }
            });
        },
        transfer: function(from, to, portion) {
            if (confirm("Are you sure you want to transfer " + from + " to " + to +"?")) {
                this.disabled = true;
                let _this = this;
                axios.get(this.tr, {
                    params: {
                        from: from.toUpperCase(),
                        to: to.toUpperCase(),
                        portion: portion,
                    }
                }).then(function (response) {
                   _this.getBalance(from);
                   _this.getBalance(to);
                   _this.disabled = !response.data;
                });
            }
        },
        inUSD: function(symbol, amount, which) {
            let _this = this;
            axios.get(this.pr, {
                params: {
                    symbol: symbol,
                }
            }).then(function (response) {
                _this.bal[which + "USD"] = response.data * amount;
            });
        },
        getLimitOrders: function() {
            let _this = this;
            axios.get('limit_orders', {
                params: {
                    symbol1: this.symbol1,
                    symbol2: this.symbol2,
                }
            }).then(function(response) {
                if(response.data.success) {
                    Object.values(response.data[0]).forEach(function(order) {
                        if (order.type === 'LIMIT' && order.side === 'BUY') {
                            _this.limitBuyPrice = parseFloat(order.price);
                            _this.limitBuyPriceFloating = parseFloat(order.price);
                            _this.amount2to1 = response.data.order_balance_percentage.symbol2;
                        }
                        if (order.type === 'LIMIT' && order.side === 'SELL') {
                            _this.stopLimitSellPriceFloating = parseFloat(order.price);
                            _this.amount1to2 = response.data.order_balance_percentage.symbol1;
                        }
                    })
                } else {
                    console.error(response.data);
                }
            })
        },
        setLimitBuy: function(price) {
            if (confirm(`when price reaches ${price} transfer from ${this.symbol2} to ${this.symbol1}?`)) {
                axios.post('limit_buy', {
                    symbol1: this.symbol1,
                    symbol2: this.symbol2,
                    price: price,
                    portion: this.amount2to1,
                    lotSize: this.lotSize,
                }).then(function(response) {
                    console.log(response);
                    this.limitBuyPrice = price;
                });
            }
        },
        cancelOrders: function(type) {
            let _this = this;
            axios.post('cancel_orders', {
                symbol1: this.symbol1,
                symbol2: this.symbol2,
                side: type,
            }).then(function() {
                _this.getLimitOrders();
            });
        },
        setStopLimitSell: function(price) {
            if (confirm(`when price reaches ${price} transfer from ${this.symbol1} to ${this.symbol2}?`)) {
                axios.post('stop_limit_sell', {
                    symbol1: this.symbol1,
                    symbol2: this.symbol2,
                    price: price,
                    portion: this.amount1to2,
                    lotSize: this.lotSize,
                }).then(function(response) {
                    console.log(response);
                    this.stopLimitSellPrice = price;
                });
            }
        },
        getLotSize: function() {
            axios.get('lot_size', {
                params: {
                    symbol1: this.symbol1,
                    symbol2: this.symbol2,
                }
            }).then(response => (this.lotSize = response.data));
        },
        slsPlus: function() {
            this.stopLimitSellPriceFloating += this.step;
        },
        slsMinus: function() {
            this.stopLimitSellPriceFloating -= this.step;
        },
        lbPlus: function() {
            this.limitBuyPriceFloating += this.step;
        },
        lbMinus: function() {
            this.limitBuyPriceFloating -= this.step;
        }
    },

    computed: {
        stopLimitSellLabel: function() {
            return 'stop limit sell (' + this.symbol1 + '->' + this.symbol2 + ')';
        },
        limitBuyLabel: function() {
            return 'limit buy (' + this.symbol2 + '->' + this.symbol1 +')';
        },
    },

    watch: {
        symbol1: function() {
            this.bal.one = "";
            this.bal.oneUSD = "";
            this.getLimitOrders();
        },
        symbol2: function() {
            this.bal.two = "";
            this.bal.twoUSD = "";
        },
        pure: function(val) {
            if (val) this.getLotSize();
        },
        pushLasts: function() {
            if (this.pushLasts.length === 2) {
                let divided = this.pushLasts[0].s1 / this.pushLasts[1].s2;
                let order = (Math.floor(1 / divided).toString().length) + 2;
                this.limitBuyPrice = null;
                this.limitBuyPriceFloating = divided.toFixed(order);
                this.stopLimitSellPrice = null;
                this.stopLimitSellPriceFloating = divided.toFixed(order);
                this.step = Math.pow(10, - order);
            }
        },
        limitBuyPrice: function() {
            this.$emit('limitBuyPrice', this.limitBuyPrice);
        },
        stopLimitSellPrice: function() {
            this.$emit('stopLimitSellPrice', this.stopLimitSellPrice);
        },
        limitBuyPriceFloating: function() {
            this.$emit('limitBuyPriceFloating', this.limitBuyPriceFloating);
        },
        stopLimitSellPriceFloating: function() {
            this.$emit('stopLimitSellPriceFloating', this.stopLimitSellPriceFloating);
        },
    }
}

</script>
<style>
.slider {
    border-radius: 0.25rem;
    -webkit-appearance: none;
    height: 32px;
    background: #d3d3d3;
    outline: none;
    -webkit-transition: .2s;
}

.slider:hover {
    opacity: 1;
}

.slider::-webkit-slider-thumb {
    border-radius: 0.25rem;
    -webkit-appearance: none;
    appearance: none;
    width: 32px;
    height: 32px;
    background: #38c172;
    cursor: pointer;
}

.slider::-moz-range-thumb {
    border-radius: 0.25rem;
    width: 32px;
    height: 32px;
    background: #38c172;
    cursor: pointer;
}
</style>
