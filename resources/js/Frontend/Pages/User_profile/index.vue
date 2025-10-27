<template>
<AppLayout>
<Banner/>
    <UserLayout />


</AppLayout>
</template>

<script>
import AppLayout from '@@/Layouts/AppLayout.vue';
import UserLayout from './Partials/UserLayout.vue';
import Banner from './Partials/Banner.vue';

export default {
    components:{
        UserLayout,
        AppLayout,
        Banner
    }

}
</script>

<style>

</style>