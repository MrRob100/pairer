<template>
    <div>
        <Scene>
            <Box :position="[0, 0, 5]"></Box>
        </Scene>
    </div>
</template>
<script>

import vb from 'vue-babylonjs';
window.Vue.use(vb);

export default {
    props: ["s1", "s2"],
    data: function() {
        return {
            s1: null,
            s2: null,
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
