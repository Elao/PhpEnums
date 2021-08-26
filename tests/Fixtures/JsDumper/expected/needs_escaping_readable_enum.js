class NeedsEscapingEnum extends ReadableEnum {
  static APOSTROPHE = 'apostrophe'
  static FORWARD_SLASH = 'forward_slash'

  static get readables() {
    return {
      [NeedsEscapingEnum.APOSTROPHE]: "'",
      [NeedsEscapingEnum.FORWARD_SLASH]: "/",
    };
  }
}
