<template>
  <section class="container-fluid px-5 px-md-6 mobileResponsiveForPadding my-5">
    <div v-if="
      !$page.props.vehiclesList ||
      $page.props.vehiclesList.length <= 0
    ">
      <div class="text-center">
        <div>
          <img src="/images/car-front-in-magnifier-glass.png" width="100" alt />
        </div>
        <div class="h5 fw-bold text-secondary pt-3">Please select your vehicle make.</div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-12       col-md-6    col-lg-4    col-xl-3" v-for="vehicle in $page.props.vehiclesList"
        :key="vehicle.ID">

        <a :href="route('auction', {
          id: vehicle.ID,
          model: stringToSlug(vehicle.MARKA_NAME),
          slug: stringToSlug(vehicle.MARKA_NAME) + '-' + stringToSlug(vehicle.MODEL_NAME),
        })" class="text-decoration-none">
        <div class="vehicle-card h-100">
          <div class="card-image">
            <img :src="getFirstImage(vehicle.IMAGES)" class="w-100 vehicle-img" alt style="object-fit: cover;" />
            <!-- <div class="status-badge" :class="{ 'sold': vehicle.STATUS === 'SOLD', 'not-sold': vehicle.STATUS !== 'SOLD' }">
                {{ vehicle.STATUS === 'SOLD' ? 'Sold' : 'Not Sold' }}
              </div> -->
          </div>
          <div class="card-body">
            <h5 class="card-title fw-bold mb-3">
              {{ vehicle.MARKA_NAME }} {{ vehicle.MODEL_NAME }} <span v-if="sanitizeGrade(vehicle.GRADE)">
                – {{ sanitizeGrade(vehicle.GRADE) }}
              </span>
            </h5>

            <div class="vehicle-details">
              <div class="detail-item">
                <span class="detail-label">Year</span>
                <span class="detail-value">{{ vehicle.YEAR }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Lot No</span>
                <span class="detail-value">{{ vehicle.LOT }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Auction</span>
                <span class="detail-value">{{ vehicle.AUCTION }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Date</span>
                <span class="detail-value">{{ vehicle.AUCTION_DATE }}</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Mileage</span>
                <span class="detail-value">{{ vehicle.MILEAGE }} KM</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Engine</span>
                <span class="detail-value">{{ vehicle.ENG_V }} CC</span>
              </div>
              <div class="detail-item">
                <span class="detail-label">Color</span>
                <span class="detail-value">{{ vehicle.COLOR }}</span>
              </div>
              <div class="detail-item">
                <Link :href="$root.getUser()
                  ? route('auction', {
                    id: vehicle.ID,
                    model: $root.stringToSlug(vehicle.MARKA_NAME),
                    slug: $root.stringToSlug(vehicle.MARKA_NAME) + '-' + stringToSlug(vehicle.MODEL_NAME),
                  })
                  : route('user.login')
                  " class="d-flex justify-content-center align-items-center h-100 btn btn-seemore detail-label">MORE
                DETAILS
                </Link>
                <!-- <span class="detail-value">{{ vehicle.STATUS }}</span> -->
              </div>
            </div>
          </div>
        </div>
      </a>
      </div>
    </div>

    <div v-if="$page.props.vehiclesList?.length > 0" class="mt-4">
      <Pagination :total="parseInt($page.props.vehiclesListCount ?? '0')"
        :currentPage="parseInt($page.props?.requestQuery?.page ?? '1')" :lastPage="computedLastPage"
        :firstItem="(($page.props?.requestQuery?.page ?? 1) - 1) * 12 + 1"
        :lastItem="Math.min($page.props.vehiclesListCount, ($page.props?.requestQuery?.page ?? 1) * 12)"
        @goto="gotoPage" />
    </div>
  </section>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import Pagination from "../../../components/Pagination.vue";

export default {
  components: {
    Link,
    Pagination
  },
  data() {
    return {
      index: ""
    };
  },
  computed: {
    sanitizedGrade() {
      const raw = this.vehicle.GRADE || ''
      return raw.replace(/&[^;]+;/g, '').trim()
    },
    computedLastPage() {
      let lastPage = Math.max(
        1,
        Math.ceil((this.$page.props.vehiclesListCount ?? 0) / 12)
      );
      console.log("Computed Last Page:", lastPage);
      return lastPage;
    }
  },
  methods: {
    sanitizeGrade(raw = '') {
      return String(raw).replace(/&[^;]+;/g, '').trim()
    },
    stringToSlug(str) {
      return str
        .toLowerCase() // Convert the string to lowercase
        .trim() // Remove whitespace from both ends
        .replace(/[^\w\s-]/g, "") // Remove all non-word characters (except for hyphens and spaces)
        .replace(/\s+/g, "-") // Replace spaces with hyphens
        .replace(/--+/g, "-") // Replace multiple hyphens with a single hyphen
        .replace(/^-+|-+$/g, ""); // Remove hyphens from the start and end of the string
    },
    getFirstImage(imgStr) {
      const strArray = imgStr.split("#");
      if (strArray.length > 0) {
        return strArray[0].replace("&h=50", "");
      } else {
        return "";
      }
    },
    gotoPage(page) {
      this.$inertia.reload({
        method: "POST",
        data: { ...this.$page.props?.requestQuery, page: page, _method: "GET" },
        onSuccess: () => {
          this.loading = false;
          window.scrollTo({ top: 0, behavior: "smooth" });
        }
      });
    }
  }
};
</script>

<style scoped>
.vehicle-card {
  background: #f0f3f7;
  border: .5px solid #a1bad6;
  border-radius: 12px;
  box-shadow:
    0 4px 10px rgba(0, 0, 0, 0.08),
    0 8px 20px rgba(0, 0, 0, 0.12);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  overflow: hidden;
  cursor: pointer;
  position: relative;
}

.vehicle-card::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: transparent;
  z-index: 1;
}

.vehicle-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
}

.card-image {
  position: relative;
  overflow: hidden;
}

.status-badge {
  position: absolute;
  top: 10px;
  left: 10px;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: bold;
  color: white;
  z-index: 2;
}

.status-badge.sold {
  background-color: rgba(220, 53, 69, 0.9);
}

.status-badge.not-sold {
  background-color: rgba(40, 167, 69, 0.9);
}

.card-body {
  padding: 1.5rem;
}

.card-title {
  font-size: 1.25rem;
  color: #333;
  line-height: 1.4;
}

.vehicle-details {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.detail-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.detail-label {
  font-size: 0.8rem;
  color: #6c757d;
}

.detail-value {
  font-size: 0.9rem;
  font-weight: 600;
  color: #333;
}

.btn-danger {
  background-color: #dc3545;
  border: none;
  padding: 0.75rem;
  font-weight: 600;
  transition: background-color 0.3s ease;
}

.btn-danger:hover {
  background-color: #bb2d3b;
}

@media (max-width: 767px) {
  .mobileResponsiveForPadding {
    padding-left: 1rem;
    padding-right: 1rem;
  }

  .vehicle-details {
    grid-template-columns: 1fr;
  }
}


.btn-seemore {
  background-color: black;
  color: white;
}





.px-md-6 {
  padding-left: 4rem !important;
  padding-right: 4rem !important;
}

@media (min-width: 768px) {
  .px-md-6 {
    padding-left: 4rem !important;
    padding-right: 4rem !important;
  }
}
</style>