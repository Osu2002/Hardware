<template>
    <div class="multi-select" @click.stop="openDropdown">
        <div class="tags">
            <span v-for="(item, idx) in selected" :key="idx" class="tag">
                {{ item.name }}
                <button type="button" @click.stop="removeTag(item)">×</button>
            </span>
            <input v-model="search" @input="openDropdown" @keydown.enter.prevent="selectBySearch"
                :placeholder="selected.length ? '' : 'Type or select…'" class="input" />
        </div>

        <ul v-if="isOpen" class="options-list">
            <li v-for="(opt, idx) in filteredOptions" :key="opt.id" @click="selectOption(opt)" class="option">
                {{ opt.name }}
            </li>
            <li v-if="filteredOptions.length === 0" class="no-option">No matches</li>
        </ul>
    </div>
</template>

<script>
export default {
    name: 'MultiSelect',
    props: {
        modelValue: {
            type: Array,
            default: () => []
        },
        options: {
            type: Array,
            required: true
        }
    },
    emits: ['update:modelValue'],
    data() {
        return {
            search: '',
            isOpen: false
        };
    },
    computed: {
        selected() {
            return this.modelValue;
        },
        filteredOptions() {
            const q = this.search.toLowerCase();
            return this.options
                .filter(o => o.name.toLowerCase().includes(q))
                .filter(o => !this.selected.some(s => s.id === o.id));
        }
    },
    methods: {
        openDropdown() {
            this.isOpen = true;
        },
        selectOption(opt) {
            this.$emit('update:modelValue', [...this.selected, opt]);
            this.search = '';
            this.isOpen = false;
        },
        removeTag(opt) {
            this.$emit('update:modelValue', this.selected.filter(s => s.id !== opt.id));
        },
        selectBySearch() {
            const match = this.filteredOptions.find(
                o => o.name.toLowerCase() === this.search.toLowerCase()
            );
            if (match) {
                this.selectOption(match);
            }
        },
        closeOnOutside(event) {
            if (!this.$el.contains(event.target)) {
                this.isOpen = false;
            }
        }
    },
    mounted() {
        document.addEventListener('click', this.closeOnOutside);
    },
    beforeUnmount() {
        document.removeEventListener('click', this.closeOnOutside);
    }
};
</script>

<style scoped>
.multi-select {
    position: relative;
    border: 1px solid #ccc;
    border-radius: 4px;
    padding: 4px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    min-height: 2.5em;
}

.input::placeholder {
    color: #3f3f3f54;
    /* brighter than default */
    opacity: 1;
}

.tags {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    flex: 1;
}

.tag {
    background: #e0e0e0;
    padding: 2px 6px;
    border-radius: 3px;
    display: flex;
    align-items: center;
}

.tag button {
    border: none;
    background: none;
    margin-left: 4px;
    cursor: pointer;
}

.input {
    border: none;
    outline: none;
    flex: 1;
    min-width: 80px;
}

.options-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ccc;
    max-height: 150px;
    overflow-y: auto;
    z-index: 10;
    margin: 0;
    padding: 0;
    list-style: none;
}

.option {
    padding: 6px 8px;
    cursor: pointer;
}

.option:hover {
    background: #f5f5f5;
}

.no-option {
    padding: 6px 8px;
    color: #999;
}
</style>