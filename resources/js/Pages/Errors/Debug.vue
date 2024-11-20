<script setup>
import { ref } from 'vue'

const props = defineProps({
  exception: {
    type: Object,
    required: true
  },
  backtrace: {
    type: Array,
    required: true
  }
})

const selectedFrame = ref(props.backtrace[0])
</script>

<template>
  <div class="container-fluid py-3">
    <!-- Exception Header -->
    <div class="row mb-3">
      <div class="col">
        <div class="card">
          <div class="card-body d-flex justify-content-between">
            <div>
              <span class="badge bg-secondary mb-2">
                {{ exception.class }}
              </span>
              <h5 class="card-title fw-bold text-danger mb-2">
                {{ exception.message }}
              </h5>
              <p class="text-muted small mb-0">
                {{ exception.file }}:{{ exception.line }}
              </p>
            </div>
            <small class="text-muted">
              PHP {{ props.phpVersion }}
            </small>
          </div>
        </div>
      </div>
    </div>

    <!-- Stack Trace and Code -->
    <div class="row">
      <!-- Stack Trace List -->
      <div class="col-4">
        <div class="card">
          <div class="card-header py-2">
            <h6 class="mb-0">Stack Trace</h6>
          </div>
          <div class="list-group list-group-flush" style="max-height: calc(100vh - 250px); overflow-y: auto;">
            <button
                v-for="(frame, index) in backtrace"
                :key="index"
                class="list-group-item list-group-item-action"
                :class="{ 'active': selectedFrame === frame }"
                @click="selectedFrame = frame"
            >
              <div class="small">
                <div class="text-truncate" :class="{ 'text-white': selectedFrame === frame }">
                  {{ frame.class || '' }}{{ frame.function }}
                </div>
                <div class="text-truncate small opacity-75">
                  {{ frame.file }}:{{ frame.line }}
                </div>
              </div>
            </button>
          </div>
        </div>
      </div>

      <!-- Code Snippet -->
      <div class="col-8">
        <div class="card">
          <div class="card-header py-2">
            <h6 class="mb-0 text-truncate">
              {{ selectedFrame.file }}:{{ selectedFrame.line }}
            </h6>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-sm mb-0">
                <tbody>
                <tr
                    v-for="(line, lineNum) in selectedFrame.snippet.lines"
                    :key="lineNum"
                    :class="{
                      'table-danger': lineNum === selectedFrame.snippet.errorLine
                    }"
                >
                  <td
                      class="text-muted user-select-none text-end"
                      style="width: 1%; padding-right: 1rem;"
                  >
                    {{ lineNum }}
                  </td>
                  <td
                      class="font-monospace text-nowrap"
                      :class="{
                        'text-dark': lineNum + '' === selectedFrame.snippet.errorLine + '',
                        'text-secondary': lineNum + '' !== selectedFrame.snippet.errorLine + '',
                        'bg-info-subtle': lineNum + '' === selectedFrame.snippet.errorLine + '',
                      }"
                  >
                    <pre><code>{{ line }}</code></pre>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
/* Scrollbary */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #ddd;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #ccc;
}

/* Aktywny element listy */
.list-group-item.active {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

/* Poprawka dla długich linii kodu */
.table {
  width: max-content;
  min-width: 100%;
}
pre {
  margin-bottom: 0;
}

/* Monospace dla kodu */
.font-monospace {
  font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}
</style>