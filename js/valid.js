function num_only(input, filter) {
    for (var i = 0; i < input.length; i++) {
        ["input"].forEach(function(event) {
            input[i].addEventListener(event, function() {
                if (!filter(this.value)) {
                    this.value = "";
                }
            });
        });
    }
}
num_only(document.getElementsByClassName('num'), function(value) {
    return /^\d*$/.test(value);
});
