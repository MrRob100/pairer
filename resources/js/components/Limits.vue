<template>
    <div class="mt-3">
        <h3>UPPER</h3>
        <div>Outer:
            <input type="number" :value="upper.outer">
        </div>
        <br>
        <div>Inner:
            <input type="number" :value="upper.inner">
        </div>
        <br>
        <h3>LOWER</h3>
        <div>Outer:
            <input type="number" :value="lower.inner">
        </div>
        <br>
        <div>Inner:
            <input type="number" :value="lower.outer">
        </div>
        <br>
    </div>
</template>

<script>
export default {
    props: [
        "s1",
        "s2",
    ],
    data: function() {
        return {
            upper: {
                inner: null,
                outer: null,
            },
            lower: {
                inner: null,
                outer: null,
            }
        };
    },

    mounted() {

    },

    methods: {

    },

    watch: {

    }

}

</script>
