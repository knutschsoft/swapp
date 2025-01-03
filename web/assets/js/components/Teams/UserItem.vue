<template>
    <span
        v-if="user"
        :class="{'text-muted': !user.isEnabled}"
        class="d-inline-flex align-items-center"
    >
      <span>
        {{ user.username }}
      </span>
      <mdicon
          v-if="!user.isEnabled"
          name="AccountOff"
          class="text-muted d-inline-flex align-items-center"
          title="Account ist aktuell nicht aktiviert."
          size="16"
      />
      <span class="d-inline-block mr-1">
        {{ appendComma ? ', ' : '' }}
      </span>
    </span>
</template>
<script setup lang="ts">
"use strict";
import {User} from "../../model";

defineProps<{
    user: User,
    appendComma: boolean
}>()

</script>
