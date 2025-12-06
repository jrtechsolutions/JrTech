import { motion } from 'framer-motion';
import { Target, Eye, Heart, Users, Award, Lightbulb, ArrowRight } from 'lucide-react';

const values = [
  {
    icon: Target,
    title: 'Missão',
    description: 'Oferecer soluções tecnológicas inovadoras e personalizadas que impulsionem o crescimento e a eficiência dos nossos clientes.',
  },
  {
    icon: Eye,
    title: 'Visão',
    description: 'Ser referência em tecnologia no mercado brasileiro, reconhecidos pela excelência, inovação e compromisso com resultados.',
  },
  {
    icon: Heart,
    title: 'Valores',
    description: 'Integridade, inovação, qualidade, foco no cliente e compromisso com resultados que transformam negócios.',
  },
];

const stats = [
  { value: '10+', label: 'Anos de Experiência', icon: Award },
  { value: '200+', label: 'Clientes Satisfeitos', icon: Users },
  { value: '500+', label: 'Projetos Entregues', icon: Lightbulb },
];

export default function AboutSection() {
  const scrollToContact = () => {
    const element = document.querySelector('#contato');
    if (element) {
      element.scrollIntoView({ behavior: 'smooth' });
    }
  };

  return (
    <section id="sobre" className="relative py-24 bg-slate-950 overflow-hidden">
      {/* Background */}
      <div className="absolute inset-0">
        <div className="absolute top-0 right-1/4 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl" />
        <div className="absolute bottom-1/4 left-1/4 w-80 h-80 bg-cyan-500/5 rounded-full blur-3xl" />
      </div>

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid lg:grid-cols-2 gap-16 items-center">
          {/* Content */}
          <motion.div
            initial={{ opacity: 0, x: -30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            <span className="inline-block px-4 py-1.5 bg-blue-500/10 border border-blue-500/20 rounded-full text-blue-400 text-sm font-medium mb-4">
              Sobre Nós
            </span>
            <h2 className="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-6">
              Tecnologia que{' '}
              <span className="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
                impulsiona
              </span>{' '}
              negócios
            </h2>
            <div className="space-y-4 text-slate-400 leading-relaxed">
              <p>
                A <span className="text-white font-semibold">JR Technology Solutions</span> nasceu 
                da paixão por tecnologia e do desejo de transformar a forma como empresas 
                utilizam recursos digitais para crescer e se destacar no mercado.
              </p>
              <p>
                Com uma equipe de profissionais altamente qualificados e certificados, 
                oferecemos soluções completas em infraestrutura de TI, segurança digital, 
                cloud computing e desenvolvimento de sistemas que realmente fazem a diferença.
              </p>
              <p>
                Nosso compromisso é entregar não apenas tecnologia, mas resultados 
                mensuráveis que contribuam diretamente para o sucesso do seu negócio.
              </p>
            </div>

            <button
              onClick={scrollToContact}
              className="mt-8 inline-flex items-center text-blue-400 hover:text-blue-300 font-medium group"
            >
              Conheça nossa história
              <ArrowRight className="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform" />
            </button>
          </motion.div>

          {/* Stats & Image */}
          <motion.div
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="relative"
          >
            <div className="relative bg-gradient-to-br from-slate-800/50 to-slate-900/50 backdrop-blur-sm rounded-3xl p-8 border border-slate-700/50">
              {/* Decorative Elements */}
              <div className="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl opacity-20 blur-xl" />
              <div className="absolute -bottom-4 -left-4 w-20 h-20 bg-gradient-to-br from-purple-500 to-blue-500 rounded-xl opacity-20 blur-xl" />

              {/* Stats Grid */}
              <div className="grid grid-cols-3 gap-4 mb-8">
                {stats.map((stat, index) => (
                  <motion.div
                    key={stat.label}
                    initial={{ opacity: 0, y: 20 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ delay: index * 0.1 }}
                    className="text-center p-4 bg-slate-800/50 rounded-xl border border-slate-700/30"
                  >
                    <stat.icon className="w-6 h-6 text-blue-400 mx-auto mb-2" />
                    <div className="text-2xl font-bold text-white">{stat.value}</div>
                    <div className="text-xs text-slate-500">{stat.label}</div>
                  </motion.div>
                ))}
              </div>

              {/* Image */}
              <div className="relative h-64 rounded-2xl overflow-hidden">
                <img
                  src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&q=80"
                  alt="Equipe JR Technology Solutions"
                  className="w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent" />
                <div className="absolute bottom-4 left-4 right-4">
                  <p className="text-white font-semibold">Equipe JR Technology</p>
                  <p className="text-slate-300 text-sm">Profissionais dedicados ao seu sucesso</p>
                </div>
              </div>
            </div>
          </motion.div>
        </div>

        {/* Mission, Vision, Values */}
        <div className="mt-24 grid md:grid-cols-3 gap-6">
          {values.map((item, index) => (
            <motion.div
              key={item.title}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              className="group"
            >
              <div className="h-full p-6 bg-gradient-to-br from-slate-800/30 to-slate-900/30 backdrop-blur-sm rounded-2xl border border-slate-800 hover:border-blue-500/30 transition-all duration-300">
                <div className="inline-flex p-3 rounded-xl bg-gradient-to-br from-blue-500/10 to-cyan-500/10 border border-blue-500/20 mb-4">
                  <item.icon className="w-6 h-6 text-blue-400" />
                </div>
                <h3 className="text-xl font-semibold text-white mb-3 group-hover:text-blue-400 transition-colors">
                  {item.title}
                </h3>
                <p className="text-slate-400 leading-relaxed">
                  {item.description}
                </p>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  );
}
