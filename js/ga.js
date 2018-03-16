
	var map;
	var directionsDisplay = null;
	var directionsService;
	var polylinePath;

	var prevNodes = [];
	var markers = [];
	var durations = [];

	// GA code
	var ga = {
	    // Default config
	    "crossoverRate": 0.5,
	    "mutationRate": 0.1,
	    "populationSize": 50,
	    "tournamentSize": 5,
	    "elitism": true,
	    "maxGenerations": 50,
	    
	    "tickerSpeed": 60,
	
	    // Loads config from HTML inputs
	    "getConfig": function() {
	        ga.crossoverRate = crossoverRate;
	        ga.mutationRate = mutationRate;
	        ga.populationSize = populationSize;
	        ga.elitism = 1;
	        ga.maxGenerations = maxGeneration;
	    },
	    
	    // Evolves given populationnodes
	    "evolvePopulation": function(population, generationCallBack, completeCallBack) {        
	        // Start evolution
	        var generation = 1;
	        var evolveInterval = setInterval(function() {
	            if (generationCallBack != undefined) {
	                generationCallBack({
	                    population: population,
	                    generation: generation,
	                });
	            }
	
	            // Evolve population
	            population = population.crossover();
	            population.mutate();
	            generation++;
	            
	            // If max generations passed
	            if (generation > ga.maxGenerations) {
	                // Stop looping
	                clearInterval(evolveInterval);
	                
	                if (completeCallBack != undefined) {
	                    completeCallBack({
	                        population: population,
	                        generation: generation,
	                    });
	                }
	            }
	        }, ga.tickerSpeed);
	    },
	
	    // Population class
	    "population": function() {
	        // Holds individuals of population
	        this.individuals = [];
	    
	        // Initial population of random individuals with given chromosome length
	        this.initialize = function(chromosomeLength) {
	            this.individuals = [];
	    
	            for (var i = 0; i < ga.populationSize; i++) {
	                var newIndividual = new ga.individual(chromosomeLength);
	                newIndividual.initialize();
	               // console.log(newIndividual);
	                this.individuals.push(newIndividual);
	            }
	        };
	        
	        // Mutates current population
	        this.mutate = function() {
	            var fittestIndex = this.getFittestIndex();
	
	            for (index in this.individuals) {
	                // Don't mutate if this is the elite individual and elitism is enabled 
	                if (ga.elitism != true || index != fittestIndex) {
	                    this.individuals[index].mutate();
	                }
	            }
	        };
	
	        // Applies crossover to current population and returns population of offspring
	        this.crossover = function() {
	            // Create offspring population
	            var newPopulation = new ga.population();
	            
	            // Find fittest individual
	            var fittestIndex = this.getFittestIndex();
	            for (index in this.individuals) {
	                // Add unchanged into next generation if this is the elite individual and elitism is enabled
	                
	                if (ga.elitism == true && index == fittestIndex) {
	                    // Replicate individual
	                    var eliteIndividual = new ga.individual(this.individuals[index].chromosomeLength);
	                    eliteIndividual.setChromosome(this.individuals[index].chromosome.slice());
	                    //console.log('fitest', this.individuals[index].chromosome.slice());
	                    newPopulation.addIndividual(eliteIndividual);
	                } else {
	                    // Select mate
	                    var parent = this.tournamentSelection();
	                    // Apply crossover
	                  //  console.log('nofitest after', this.individuals[index].chromosome.slice());
	                    this.individuals[index].crossover(parent, newPopulation);
	                  //  console.log('nofitest after', this.individuals[index].chromosome.slice());
	                }
	            }
	            
	            return newPopulation;
	        };
	
	        // Adds an individual to current population
	        this.addIndividual = function(individual) {
	            this.individuals.push(individual);
	        };
	
	        // Selects an individual with tournament selection
	        this.tournamentSelection = function() {
	            // Randomly order population
	            for (var i = 0; i < this.individuals.length; i++) {
	                var randomIndex = Math.floor(Math.random() * this.individuals.length);
	                var tempIndividual = this.individuals[randomIndex];
	                this.individuals[randomIndex] = this.individuals[i];
	                this.individuals[i] = tempIndividual;
	            }
	
	            // Create tournament population and add individuals
	            var tournamentPopulation = new ga.population();
	            for (var i = 0; i < ga.tournamentSize; i++) {
	                tournamentPopulation.addIndividual(this.individuals[i]);
	            }
	
	            return tournamentPopulation.getFittest();
	        };
	        
	        // Return the fittest individual's population index
	        this.getFittestIndex = function() {
	            var fittestIndex = 0;
	
	            // Loop over population looking for fittest
	            for (var i = 1; i < this.individuals.length; i++) {
	                if (this.individuals[i].calcFitness() > this.individuals[fittestIndex].calcFitness()) {
	                    fittestIndex = i;
	                }
	            }
	
	            return fittestIndex;
	        };
	
	        // Return fittest individual
	        this.getFittest = function() {
	            return this.individuals[this.getFittestIndex()];
	        };
	    },
	
	    // Individual class
	    "individual": function(chromosomeLength) {
	        this.chromosomeLength = chromosomeLength;
	        this.fitness = null;
	        this.chromosome = [];
	
	        // Initialize random individual
	        this.initialize = function() {
	            this.chromosome = [];
	
	            // Generate random chromosome
	            for (var i = 0; i < this.chromosomeLength; i++) {
	                this.chromosome.push(i);
	            }
	            for (var i = 1; i < this.chromosomeLength; i++) {
	                var randomIndex = Math.floor(Math.random() * (this.chromosomeLength )) ;
	                if(randomIndex>0) {
		                var tempNode = this.chromosome[randomIndex];
		                this.chromosome[randomIndex] = this.chromosome[i];
		                this.chromosome[i] = tempNode;
	                }
	            }
	        };
	        
	        // Set individual's chromosome
	        this.setChromosome = function(chromosome) {
	            this.chromosome = chromosome;
	        };
	        
	        // Mutate individual
	        this.mutate = function() {
	            this.fitness = null;
	            
	            // Loop over chromosome making random changes
	            for (index in this.chromosome) {
	                if (ga.mutationRate > Math.random() && index>0) {
	                    var randomIndex = Math.floor(Math.random() * (this.chromosomeLength));
	                    if(randomIndex>0) {
		                    var tempNode = this.chromosome[randomIndex];
		                    this.chromosome[randomIndex] = this.chromosome[index];
		                    this.chromosome[index] = tempNode;
	                    	
	                    }
	                }
	            }
	        };
	        
	        // Returns individuals route distance
	        this.getDistance = function() {
	            var totalDistance = 0;
	console.log('getDistance', this.chromosome);
	            for (index in this.chromosome) {
	                var startNode = this.chromosome[index];
	                var endNode = this.chromosome[0];
	                if ((parseInt(index) + 1) < this.chromosome.length) {
	                    endNode = this.chromosome[(parseInt(index) + 1)];
	                }
	
	                totalDistance += durations[startNode][endNode];
	            }
	            
	            totalDistance += durations[startNode][endNode];
	            
	            return totalDistance;
	        };
	
	        // Calculates individuals fitness value
	        this.calcFitness = function() { 
	            if (this.fitness != null) {
	                return this.fitness;
	            }
	        
	            var totalDistance = this.getDistance();
	
	            this.fitness = 1 / totalDistance;
	            return this.fitness;
	        };
	
	        // Applies crossover to current individual and mate, then adds it's offspring to given population
	        this.crossover = function(individual, offspringPopulation) {
	            var offspringChromosome = [];
	
	            // Add a random amount of this individual's genetic information to offspring
	            var startPos = Math.floor(this.chromosome.length * Math.random() );
	            var endPos = Math.floor(this.chromosome.length * Math.random());
	
				var i = startPos;
	            while (i != endPos) {
	                
	               if(i!=0) {
		                offspringChromosome[i] = individual.chromosome[i];
	               }
	                
	                i++
	
	                if (i >= this.chromosome.length) {
	                    i = 0;
	                }
	            }
	
	            // Add any remaining genetic information from individual's mate
	            //console.log('indi', individual.chromosome);
	            for (parentIndex in individual.chromosome) {
	            	
	                var node = individual.chromosome[parentIndex];
	
	                var nodeFound = false;
	                for (offspringIndex in offspringChromosome) {
	                	
	   	            		if (offspringChromosome[offspringIndex] == node) {
		                        nodeFound = true;
		                        break;
		                    }
	                }
	
	                if (nodeFound == false) {
	                    for (var offspringIndex = 0; offspringIndex < individual.chromosome.length; offspringIndex++) {
	                        if (offspringChromosome[offspringIndex] == undefined) {
	                            offspringChromosome[offspringIndex] = node;
	                            break;
	                        }
	                    }
	                }
	            }
	
	            // Add chromosome to offspring and add offspring to population
	            var offspring = new ga.individual(this.chromosomeLength);
	            //console.log('offA',offspring.chromosome);
	            offspring.setChromosome(offspringChromosome);
	            //console.log('offB',offspring.chromosome);
	            offspringPopulation.addIndividual(offspring);
	        };
	    },
	};
	