<template>
  <div class="map-wrapper">
    <div class="row">
      <div class="col-12 col-md-6">
        <div class="row items-center q-col-gutter-sm q-mb-sm">
          <!--      <div class="col-12 col-md-6">-->
          <!--        <q-input v-model="addr" dense outlined placeholder="Buscar dirección (ej: Calle Bolívar, Oruro)"/>-->
          <!--      </div>-->
          <!--      <div class="col-6 col-md-3">-->
          <!--        <q-btn dense no-caps color="primary" label="Buscar" @click="searchAddress" icon="search" style="width:100%"/>-->
          <!--      </div>-->
          <div class="col-12 col-md-6">
            <q-input v-model.number="localValue.latitud" dense outlined label="Latitud" />
          </div>
          <div class="col-12 col-md-6">
            <q-input v-model.number="localValue.longitud" dense outlined label="Longitud" />
          </div>
          <div class="col-6 col-md-6">
            <q-btn no-caps color="primary" label="Ir" @click="flyToLatLng" icon="place" style="width: 220px" />
          </div>
          <div class="col-6 col-md-6">
            <q-btn no-caps color="primary" label="Mi ubicación" @click="locateMe" icon="my_location" style="width: 220px"/>
          </div>
          <div class="col-6 col-md-6">
            <q-btn no-caps color="info" :href="`https://www.google.com/maps/search/?api=1&query=${localValue.latitud},${localValue.longitud}`" target="_blank" label="Abrir en Google Maps" icon="open_in_new" style="width: 220px"/>
          </div>
          <div class="col-6 col-md-6">
            <q-btn no-caps color="info" :href="`${$url}/mapa?lat=${localValue.latitud}&lng=${localValue.longitud}&label=${label}`" target="_blank" label="Imprimir" icon="print" style="width: 220px"/>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6">
        <l-map
          style="height: 350px"
          v-model:zoom="zoom"
          :center="mapCenter"
          :use-global-leaflet="false"
          :options="{ attributionControl: false }"
          @click="onMapClick"
          ref="mapRef"
        >
          <!-- Control de capas -->
          <l-control-layers position="topright" />

          <!-- OpenStreetMap -->
          <l-tile-layer
            layer-type="base"
            name="OpenStreetMap"
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            attribution="&copy; OpenStreetMap contributors"
            :subdomains="['a','b','c']"
            :max-zoom="19"
            :visible="false"
          />

          <!-- Google Calle (por defecto) -->
          <l-tile-layer
            layer-type="base"
            name="Google Calle"
            url="https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}"
            attribution="Map data &copy; Google"
            :max-zoom="21"
            :visible="true"
          />

          <!-- Google Satélite -->
          <l-tile-layer
            layer-type="base"
            name="Google Satélite"
            url="https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}"
            attribution="Map data &copy; Google"
            :max-zoom="21"
            :visible="false"
          />

          <!-- Google Híbrido -->
          <l-tile-layer
            layer-type="base"
            name="Google Híbrido"
            url="https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}"
            attribution="Map data &copy; Google"
            :max-zoom="21"
            :visible="false"
          />

          <!-- Marcador -->
          <l-marker
            v-if="hasLatLng"
            :lat-lng="[Number(localValue.latitud), Number(localValue.longitud)]"
            :draggable="true"
            @moveend="onDragEnd"
          >
            <l-popup>
              <div>Lat: {{ toFix(localValue.latitud) }}<br/>Lng: {{ toFix(localValue.longitud) }}</div>
            </l-popup>
          </l-marker>
        </l-map>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, computed, ref, watch } from 'vue'
import { LMap, LTileLayer, LMarker, LPopup, LControlLayers } from '@vue-leaflet/vue-leaflet'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'
import 'leaflet-control-geocoder/dist/Control.Geocoder.css'
import 'leaflet-control-geocoder'
const addr = ref('')

// Fix iconos Vite
import markerIcon2xUrl from 'leaflet/dist/images/marker-icon-2x.png'
import markerIconUrl   from 'leaflet/dist/images/marker-icon.png'
import markerShadowUrl from 'leaflet/dist/images/marker-shadow.png'
L.Icon.Default.mergeOptions({
  iconRetinaUrl: markerIcon2xUrl,
  iconUrl: markerIconUrl,
  shadowUrl: markerShadowUrl
})
onMounted(() => {
  const map = mapRef.value?.leafletObject
  if (!map) return
  // Crea el control (usa Nominatim por defecto)
  const geocoderControl = L.Control.geocoder({
    position: 'topleft',
    defaultMarkGeocode: false
  })
    .on('markgeocode', (e) => {
      const center = e.geocode.center
      // Actualiza tu v-model
      localValue.value.latitud = Number(center.lat.toFixed(7))
      localValue.value.longitud = Number(center.lng.toFixed(7))
      map.flyTo(center, Math.max(zoom.value, 16))
    })
    .addTo(map)
})
async function searchAddress () {
  const q = (addr.value || '').trim()
  if (!q) return
  try {
    // Nominatim requiere un UA o email de contacto en “User-Agent” o “email” param
    const url = new URL('https://nominatim.openstreetmap.org/search')
    url.searchParams.set('q', q)
    url.searchParams.set('format', 'json')
    url.searchParams.set('addressdetails', '1')
    url.searchParams.set('limit', '5')      // puedes listar opciones si quieres
    url.searchParams.set('countrycodes', 'bo') // opcional, sesga a Bolivia

    const res = await fetch(url.toString(), {
      headers: { 'Accept': 'application/json' }
    })
    const data = await res.json()
    if (!Array.isArray(data) || data.length === 0) {
      return
    }
    const best = data[0]
    const lat = Number(best.lat)
    const lon = Number(best.lon)
    localValue.value.latitud = Number(lat.toFixed(7))
    localValue.value.longitud = Number(lon.toFixed(7))
    const leaflet = mapRef.value?.leafletObject
    leaflet && leaflet.flyTo([lat, lon], 17)
  } catch (e) {
    console.error(e)
  }
}

const props = defineProps({
  modelValue: { type: Object, default: () => ({ latitud: null, longitud: null }) },
  center: { type: Array, default: () => [-16.5, -68.15] },
  zoomInit: { type: Number, default: 13 },
  address: { type: String, default: '' },          // <--- NUEVO
  country: { type: String, default: 'bo' },        // <--- opcional: sesgar búsqueda por país
  minAddressLen: { type: Number, default: 6 },     // <--- evitar geocodificar textos muy cortos
  debounceMs: { type: Number, default: 600 },       // <--- para no pegarle a la API por cada tecla
  label: { type: String, default: 'Seleccionar ubicación' }
})

const emit = defineEmits(['update:modelValue','geocode:ok','geocode:error'])
defineExpose({
  geocodeAndFly,
  flyToLatLng,
  locateMe
})

async function geocodeAndFly(q) {
  const query = (q || '').trim()
  if (!query) return
  try {
    const url = new URL('https://nominatim.openstreetmap.org/search')
    url.searchParams.set('q', query)
    url.searchParams.set('format', 'json')
    url.searchParams.set('addressdetails', '1')
    if (props.country) url.searchParams.set('countrycodes', props.country)
    url.searchParams.set('limit', '1')

    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } })
    const data = await res.json()
    if (!Array.isArray(data) || data.length === 0) {
      emit('geocode:error', { query, reason: 'no-results' })
      return
    }

    const best = data[0]
    const lat = Number(best.lat)
    const lon = Number(best.lon)

    // Actualiza el v-model (latitud/longitud) del padre
    localValue.value.latitud = Number(lat.toFixed(7))
    localValue.value.longitud = Number(lon.toFixed(7))
    emit('update:modelValue', normalizeOut(localValue.value))

    // Vuela el mapa
    const leaflet = mapRef.value?.leafletObject
    leaflet && leaflet.flyTo([lat, lon], Math.max(zoom.value, 16))

    emit('geocode:ok', { query, lat, lon, raw: best })
  } catch (e) {
    console.error(e)
    emit('geocode:error', { query, reason: 'exception', error: String(e) })
  }
}


/**
 * Estado interno siempre en {latitud, longitud}.
 * Si el padre manda {lat, lng}, lo mapeamos.
 */
const normalizeIn = (mv) => ({
  latitud: mv?.latitud ?? mv?.lat ?? null,
  longitud: mv?.longitud ?? mv?.lng ?? null
})
const normalizeOut = (v) => ({
  // Emitimos ambos pares de keys para no romper nada
  latitud: v.latitud ?? null,
  longitud: v.longitud ?? null,
  lat: v.latitud ?? null,
  lng: v.longitud ?? null
})

let addressTimer = null
// watch(
//   () => props.address,
//   (next) => {
//     if (!next || next.trim().length < props.minAddressLen) return
//     clearTimeout(addressTimer)
//     addressTimer = setTimeout(() => {
//       geocodeAndFly(next)
//     }, props.debounceMs)
//   },
//   { immediate: false }
// )

const localValue = ref(normalizeIn(props.modelValue))

// Observa cambios del padre (en cualquiera de las dos formas)
watch(
  () => props.modelValue,
  (mv) => {
    const next = normalizeIn(mv)
    if (next.latitud !== localValue.value.latitud || next.longitud !== localValue.value.longitud) {
      localValue.value = next
    }
  },
  { deep: true }
)

// Emitimos al padre cuando cambian latitud/longitud locales
watch(localValue, v => emit('update:modelValue', normalizeOut(v)), { deep: true })

const mapRef = ref(null)
const zoom = ref(props.zoomInit)

const mapCenter = computed(() =>
  (localValue.value.latitud != null && localValue.value.longitud != null)
    ? [Number(localValue.value.latitud), Number(localValue.value.longitud)]
    : props.center
)

const hasLatLng = computed(() =>
  localValue.value.latitud != null && localValue.value.longitud != null &&
  Number.isFinite(Number(localValue.value.latitud)) &&
  Number.isFinite(Number(localValue.value.longitud))
)

function toFix (v, n = 6) {
  const num = Number(v)
  return Number.isFinite(num) ? num.toFixed(n) : '-'
}

function onMapClick(e) {
  const { lat, lng } = e.latlng
  localValue.value.latitud = Number(lat.toFixed(7))
  localValue.value.longitud = Number(lng.toFixed(7))
}
function onDragEnd(e) {
  const { lat, lng } = e.target.getLatLng()
  localValue.value.latitud = Number(lat.toFixed(7))
  localValue.value.longitud = Number(lng.toFixed(7))
}
function flyToLatLng() {
  if (!hasLatLng.value) return
  const leaflet = mapRef.value?.leafletObject
  leaflet && leaflet.flyTo([Number(localValue.value.latitud), Number(localValue.value.longitud)], Math.max(zoom.value, 15))
}
function locateMe() {
  if (!('geolocation' in navigator)) return
  navigator.geolocation.getCurrentPosition(pos => {
    const { latitude, longitude } = pos.coords
    localValue.value.latitud = Number(latitude.toFixed(7))
    localValue.value.longitud = Number(longitude.toFixed(7))
    const leaflet = mapRef.value?.leafletObject
    leaflet && leaflet.flyTo([Number(localValue.value.latitud), Number(localValue.value.longitud)], 16)
  })
}
</script>

<style scoped>
.map-wrapper { width: 100%; }
</style>
