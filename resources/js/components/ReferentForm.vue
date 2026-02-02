<template>
    <form @submit.prevent="submitForm">
        <Input type="text"
               id="name"
               placeholder="Name *"
               v-model="form.name"
               required
               class="mb-2" />

        <Input type="text"
               id="last_name"
               placeholder="Last Name *"
               v-model="form.last_name"
               class="mb-2"
               required />

        <Input type="email"
               id="email"
               placeholder="Email *"
               v-model="form.email"
               class="mb-2"
               ref="emailInput"
               required />

        <Input type="tel"
               id="phone"
               placeholder="Phone *"
               v-model="form.phone"
               class="mb-2"
               required />

        <p class="text-gray-500 text-xs italic mb-2">* Required field</p>

        <Button type="submit"
                variant="outline"
                :disabled="loading">
            {{ loading ? 'Saving...' : 'Save' }}
        </Button>
        
        <div v-if="showEmailError" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
            <p class="block sm:inline">This email has already been added to the shipment.</p>
        </div>
    </form>
</template>

<script setup lang="ts">
import axios from 'axios';
import { reactive, ref } from 'vue';
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'

/**
 * Define component props passed from parent
 * `shipmentId` is used for API calls
 * `scope` defines where the Referent applies (start | end)
 */ 
const props = defineProps<{
    shipmentId: number | string;
    scope: string;
}>();

/**
 * Define events emitted to parent component
 * - referent-added: fired after successful creation
 * - error: fired when an API error occurs
 */ 
const emit = defineEmits<{
    'referent-added': [data: any];
    'error': [error: any];
}>();

/**
 * Form state management
 * Using reactive to create a form object with fields
 * `scope` is pre-filled from props and not user-editable
 */ 
// 
const form = reactive({
    name: '',
    last_name: '',
    email: '',
    phone: '',
    scope: props.scope
});

/**
 * Validation errors structure
 * Each field can have multiple error messages
 */ 
interface ValidationErrors {
    [key: string]: string[]; // each field (email, name, phone…) can have multiple errors
}

/**
 * Component state variables
 * - loading: indicates form submission in progress
 * - errors: holds validation errors from API
 * - showEmailError: controls visibility of email error alert
 */
const loading = ref(false);
const errors = ref<ValidationErrors>({});
const showEmailError = ref(false);

/**
 * Submit form data to backend API
 * - Validates email uniqueness before submission
 * - Emits events based on success or failure
 * - Resets form on success
 */
const submitForm = async () => {
    loading.value = true;
    errors.value = {};

    // Pre-check to avoid creating duplicate Referents
    const emailTaken = await checkEmailExists(form.email);

    // Show error if email is already been taken and stop submission showing error message for a few seconds
    if (emailTaken) {
        showEmailError.value = true

        setTimeout(() => {
            showEmailError.value = false
        }, 5000)

        loading.value = false
        return
    }

    try {
        // Send POST request to add Referent to shipment
        const response = await axios.post(
            `/shipments/${props.shipmentId}/addReferent`,
            form
        );

        // Notify parent component of success
        emit('referent-added', {
            referent: response.data,
            scope: form.scope
        });

        // Reset form inputs
        resetForm();

    } catch (error) {
        console.error('Error saving referent:', error);
        emit('error', error);
    } finally {
        loading.value = false;
    }
};

/**
 * Check if a Referent email already exists for this shipment
 * Returns true if the email is already in use
 */
const checkEmailExists = async (email: string): Promise<boolean> => {
    if (!email) return false;

    try {
        const response = await axios.get(`/shipments/${props.shipmentId}/checkEmail`, {
            params: { email }
        });
        return response.data.exists;
    } catch (err) {
        console.error('Error checking email:', err);
        return false;
    }
};

/**
 * Reset form fields after successful submission
 * Scope is preserved from props
 */
const resetForm = () => {
    form.name = '';
    form.last_name = '';
    form.email = '';
    form.phone = '';
};
</script>