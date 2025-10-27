<template>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <!-- <div v-if="isLoading" class="loading-overlay">
            <div class="spinner"></div>
        </div> -->
        <div class="layout-container">
            <SideNavBar />
            <!-- Layout container -->
            <div id="layout-page"  class="layout-page">
                <TopNavBar />
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-fluid flex-grow-1 container-p-y">
                        <slot />
                    </div>
                </div>
            </div>
            <!-- / Layout container -->
        </div>
    </div>
    <!-- / Layout wrapper -->
</template>

<script>
import { ref, onMounted } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import TopNavBar from "@/Layouts/Partials/TopNavBar.vue";
import SideNavBar from "@/Layouts/Partials/SideNavBar.vue";
import FooterBar from "@/Layouts/Partials/FooterBar.vue";


export default {
    components: {
        TopNavBar,
        SideNavBar,
        FooterBar,
       
    },
    setup() {
        const isLoading = ref(false);

        // Corrected Inertia event listeners
        Inertia.on('start', () => {
            isLoading.value = true;
        });
        Inertia.on('finish', () => {
            isLoading.value = false;
        });

        onMounted(() => {
            feather.replace();

            $(document).ready(function () {
               


                // Initialize Select2
                if ($(".select2").length) {
                    $(".select2").each(function () {
                        $(this).wrap('<div class="position-relative"></div>').select2({
                            placeholder: "-- Select --",
                            dropdownParent: $(this).parent(),
                        });
                    });
                }

                // Initialize Selectpicker
                const selectPicker = $(".selectpicker");
                if (selectPicker.length) {
                    selectPicker.selectpicker();
                }
            });

            // Initialize Popovers
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.forEach(popoverTriggerEl => {
                new bootstrap.Popover(popoverTriggerEl, {
                    html: false,
                    sanitize: false,
                });
            });

            // Handle modal close issue
            $("body").on("hidden.bs.modal", ".modal", function () {
                if ($("body").hasClass("modal-open")) {
                    $("body").removeClass("modal-open").attr("style", "");
                }
            });

            // Handle sidebar menu toggle
           

            $(".menu-link.menu-toggle").on("click", function () {
                $(this).parent().toggleClass("open active", 300);
            });
        });

        return { isLoading };
    },
    methods: {
        switchToTeam(team) {
            this.$inertia.put(route("current-team.update"), { team_id: team.id }, { preserveState: false });
        },
        logout() {
            console.log("logout");
            // this.$inertia.post(route("logout"));
        },
    },
};
</script>
<style>
.loading-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 5px solid white;
  border-top: 5px solid transparent;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}
</style>
