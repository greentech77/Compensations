import AdminLayout from '@/Layouts/AdminLayout.vue'

// Inertia 3 prefers the layout to be assigned as the component itself.
// In Inertia 1.x this file used to be a render function `(h, page) => h(AdminLayout, ...)`,
// but `page.type.name` is no longer reachable on the new resolver result and it
// blew up with "Cannot read properties of undefined (reading 'name')".
//
// AdminLayout.vue does not actually use the old `componentName` prop, so this
// straight re-export is functionally equivalent and works on both 1.x and 3.x.
export default AdminLayout
