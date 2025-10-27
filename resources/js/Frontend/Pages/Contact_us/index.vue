<template>
  <AppLayout>

  <Banner />

    <Form />

  </AppLayout>
</template>

<script>
import AppLayout from '@@/Layouts/AppLayout.vue';
import Banner from './partials/Banner.vue';
import Form from './partials/Form.vue';

export default {
    components:{
        AppLayout,
        Banner,
        Form
    }
}
</script>

<style>

</style>