(function(window, undefined) {
  var dictionary = {
    "m-cad2274e-e7ec-4c01-b4c0-12cb4427b1e5 Slice_1": [ ["Slice 1@1x.png", "ac2aa1b8-3f51-4029-9997-b985553d6587_1517424372.png"] ]
  };

  window.jimDevelopers.lookUpSlice = function(name) {
    var imageName;
    if(dictionary.hasOwnProperty(name)) { /* search by name */
      imageName = dictionary[name];
    }
    return imageName;
  };
})(window);